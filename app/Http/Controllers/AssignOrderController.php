<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Order;
use Illuminate\Http\Request;

class AssignOrderController extends Controller
{
    /**
     * Step 1: Display listing of orders to select for assignment.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'member', 'service', 'cuttingEmployee', 'stitchingEmployee']);

        // Search filter
        if ($request->filled('q')) {
            $search = trim($request->input('q'));
            $query->where(function ($q) use ($search) {
                $numericSearch = ltrim($search, '0#');
                if (is_numeric($numericSearch)) {
                    $q->orWhere('id', (int) $numericSearch);
                }

                $q->orWhereHas('customer', function ($cq) use ($search) {
                    $cq->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('service', function ($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('cuttingEmployee', function ($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('stitchingEmployee', function ($eq) use ($search) {
                    $eq->where('name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        // Filter by Assignment status
        if ($request->filled('assignment')) {
            if ($request->input('assignment') === 'unassigned') {
                $query->whereNull('cutting_employee_id')->whereNull('stitching_employee_id');
            } elseif ($request->input('assignment') === 'partial') {
                $query->where(function ($q) {
                    $q->whereNull('cutting_employee_id')->orWhereNull('stitching_employee_id');
                });
            } elseif ($request->input('assignment') === 'assigned') {
                $query->whereNotNull('cutting_employee_id')->whereNotNull('stitching_employee_id');
            }
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $stats = [
            'total'      => Order::count(),
            'unassigned' => Order::whereNull('cutting_employee_id')->whereNull('stitching_employee_id')->count(),
            'assigned'   => Order::whereNotNull('cutting_employee_id')->whereNotNull('stitching_employee_id')->count(),
            'employees'  => Employee::where('is_active', true)->count(),
        ];

        return view('orders.assign', compact('orders', 'stats'));
    }

    /**
     * Step 2: Show cutting worker assignment page for selected orders.
     */
    public function cutting(Request $request)
    {
        $orderIds = $request->input('order_ids');

        if (empty($orderIds) && $request->has('assignments')) {
            $assignments = (array) $request->input('assignments');
            $orderIds = array_column($assignments, 'order_id');
        }

        if (is_string($orderIds)) {
            $orderIds = explode(',', $orderIds);
        }

        $orderIds = array_filter((array) $orderIds, fn($id) => is_numeric($id));

        if (empty($orderIds)) {
            return redirect()->route('assign-orders.index')
                ->with('error', 'Please select at least one order to proceed with assignment.');
        }

        $orders = Order::with(['customer.members', 'member', 'service', 'cuttingEmployee', 'stitchingEmployee'])
            ->whereIn('id', $orderIds)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('assign-orders.index')
                ->with('error', 'No valid orders found for assignment.');
        }

        // If request came from Step 1 (without back assignments/force_cutting),
        // and all selected orders are already in 'cutting' status, directly route to Step 3 (Stitching).
        if (!$request->has('assignments') && !$request->has('force_cutting')) {
            $allCutting = $orders->every(function ($order) {
                return $order->status === 'cutting' || ($order->cutting_employee_id && !$order->stitching_employee_id);
            });
            if ($allCutting) {
                return $this->stitching($request);
            }
        }

        $employees = Employee::where('is_active', true)
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get();

        $cuttingEmployees = $employees->filter(fn ($e) => $e->department && stripos($e->department->name, 'cutting') !== false)->values();
        if ($cuttingEmployees->isEmpty()) {
            $cuttingEmployees = $employees;
        }

        // Check if existing cutting assignments were passed back
        $existingCutting = [];
        if ($request->has('assignments')) {
            foreach ((array)$request->input('assignments') as $a) {
                if (!empty($a['order_id']) && isset($a['cutting_employee_id'])) {
                    $existingCutting[$a['order_id']] = $a['cutting_employee_id'];
                }
            }
        }

        return view('orders.assign_cutting', compact('orders', 'employees', 'cuttingEmployees', 'existingCutting'));
    }

    /**
     * Step 3: Show stitching worker assignment page for selected orders.
     */
    public function stitching(Request $request)
    {
        $assignments = $request->input('assignments', []);
        $orderIds = [];
        $cuttingMap = [];

        if (!empty($assignments)) {
            foreach ($assignments as $a) {
                if (!empty($a['order_id'])) {
                    $oid = (int) $a['order_id'];
                    $orderIds[] = $oid;
                    $cuttingMap[$oid] = !empty($a['cutting_employee_id']) ? (int) $a['cutting_employee_id'] : null;
                }
            }
        } else {
            $rawIds = $request->input('order_ids', []);
            if (is_string($rawIds)) $rawIds = explode(',', $rawIds);
            $orderIds = array_filter((array)$rawIds, fn($id) => is_numeric($id));
        }

        if (empty($orderIds)) {
            return redirect()->route('assign-orders.index')
                ->with('error', 'Please select orders to configure stitching workers.');
        }

        $orders = Order::with(['customer.members', 'member', 'service', 'cuttingEmployee', 'stitchingEmployee'])
            ->whereIn('id', $orderIds)
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('assign-orders.index')
                ->with('error', 'No valid orders found for stitching assignment.');
        }

        $employees = Employee::where('is_active', true)
            ->with(['designation', 'department'])
            ->orderBy('name')
            ->get();

        $stitchingEmployees = $employees->filter(fn ($e) => $e->department && stripos($e->department->name, 'stitching') !== false)->values();
        if ($stitchingEmployees->isEmpty()) {
            $stitchingEmployees = $employees;
        }

        $cuttingEmployees = $employees->filter(fn ($e) => $e->department && stripos($e->department->name, 'cutting') !== false)->values();
        if ($cuttingEmployees->isEmpty()) {
            $cuttingEmployees = $employees;
        }

        return view('orders.assign_stitching', compact('orders', 'employees', 'stitchingEmployees', 'cuttingEmployees', 'cuttingMap'));
    }

    /**
     * Save worker assignments (Cutting only, Stitching only, or both).
     */
    public function save(Request $request)
    {
        $request->validate([
            'assignments'                         => 'required|array|min:1',
            'assignments.*.order_id'              => 'required|exists:orders,id',
            'assignments.*.cutting_employee_id'   => 'nullable|exists:employees,id',
            'assignments.*.stitching_employee_id' => 'nullable|exists:employees,id',
            'assignments.*.status'                => 'nullable|in:pending,cutting,stitching,in_progress,completed,delivered,cancelled',
        ]);

        $updatedCount = 0;

        foreach ($request->input('assignments') as $assignment) {
            $order = Order::find($assignment['order_id']);
            if (!$order) continue;

            $hasCutting = array_key_exists('cutting_employee_id', $assignment);
            $hasStitching = array_key_exists('stitching_employee_id', $assignment);

            if ($hasCutting) {
                $cuttingId = !empty($assignment['cutting_employee_id']) ? (int)$assignment['cutting_employee_id'] : null;
                $order->cutting_employee_id = $cuttingId;
            }
            if ($hasStitching) {
                $stitchingId = !empty($assignment['stitching_employee_id']) ? (int)$assignment['stitching_employee_id'] : null;
                $order->stitching_employee_id = $stitchingId;
            }

            // Sync legacy assigned_employee_id
            $order->assigned_employee_id = $order->stitching_employee_id ?: $order->cutting_employee_id ?: null;

            // Auto-update status based on worker assignments
            if (!empty($assignment['status'])) {
                $order->status = $assignment['status'];
            } elseif (!in_array($order->status, ['completed', 'delivered', 'cancelled'])) {
                if ($order->cutting_employee_id && $order->stitching_employee_id) {
                    // Both cutting & stitching assigned -> In Progress
                    $order->status = 'in_progress';
                } elseif ($order->stitching_employee_id) {
                    // Stitching assigned -> In Progress
                    $order->status = 'in_progress';
                } elseif ($order->cutting_employee_id) {
                    // Only cutting assigned -> In Cutting
                    $order->status = 'cutting';
                } else {
                    // Neither assigned -> Pending
                    $order->status = 'pending';
                }
            }

            $order->save();
            $updatedCount++;
        }

        $message = "Worker assignments for {$updatedCount} order(s) updated successfully.";
        return redirect()->route('assign-orders.index')->with('success', $message);
    }
}
