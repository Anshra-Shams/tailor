<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Order;
use App\Models\Salary;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmployeeLedgerController extends Controller
{
    public function index(Request $request)
    {
        $employeeId = $request->query('employee_id');
        $fromDate = $request->query('from_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to_date', Carbon::today()->format('Y-m-d'));
        $activeTab = $request->query('tab', 'all');

        $allEmployees = Employee::with(['department', 'designation'])
            ->orderBy('name')
            ->get();

        if (!$employeeId) {
            $employee = null;
            return view('employee_ledger.index', compact(
                'allEmployees',
                'employee',
                'fromDate',
                'toDate',
                'activeTab'
            ));
        }

        $employee = Employee::with(['department', 'designation'])->findOrFail($employeeId);
        return $this->renderLedger($employee, $fromDate, $toDate, $activeTab, $allEmployees);
    }

    public function show(Employee $employee, Request $request)
    {
        $fromDate = $request->query('from_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to_date', Carbon::today()->format('Y-m-d'));
        $activeTab = $request->query('tab', 'all');

        $allEmployees = Employee::with(['department', 'designation'])
            ->orderBy('name')
            ->get();

        return $this->renderLedger($employee, $fromDate, $toDate, $activeTab, $allEmployees);
    }

    public function print(Employee $employee, Request $request)
    {
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');

        $employee->load(['department', 'designation']);

        // Salary records query
        $salariesQuery = Salary::with('account')
            ->where('employee_id', $employee->id);

        if ($fromDate) {
            $salariesQuery->whereDate('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $salariesQuery->whereDate('payment_date', '<=', $toDate);
        }

        $salaries = $salariesQuery->orderByDesc('payment_date')->get();

        // Orders query
        $ordersQuery = Order::with(['customer', 'service'])
            ->where(function ($q) use ($employee) {
                $q->where('assigned_employee_id', $employee->id)
                  ->orWhere('cutting_employee_id', $employee->id)
                  ->orWhere('stitching_employee_id', $employee->id);
            });

        if ($fromDate) {
            $ordersQuery->whereDate('order_date', '>=', $fromDate);
        }
        if ($toDate) {
            $ordersQuery->whereDate('order_date', '<=', $toDate);
        }

        $orders = $ordersQuery->orderByDesc('created_at')->get();

        // Attendance stats
        $attendancesQuery = Attendance::where('employee_id', $employee->id);
        if ($fromDate) {
            $attendancesQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $attendancesQuery->whereDate('date', '<=', $toDate);
        }
        $attendances = $attendancesQuery->orderByDesc('date')->get();

        $completedSuits = (int) $orders->whereIn('status', ['completed', 'delivered'])->sum('quantity');
        $totalSuits = (int) $orders->sum('quantity');
        $rate = (float) $employee->salary;
        $totalAssignedOrders = $orders->count();
        $totalWorkQuantity = $totalSuits > 0 ? $totalSuits : $totalAssignedOrders;

        $assignedWorkAmount = $employee->salary_type === 'project' 
            ? ($totalWorkQuantity * $rate) 
            : (float) $orders->sum(fn($o) => (float)$o->price * (int)$o->quantity);

        $totalWorkEarned = $employee->salary_type === 'project' 
            ? $assignedWorkAmount 
            : (float) $salaries->sum('basic_salary');

        $totalDisbursed = (float) $salaries->where('status', 'paid')->sum('net_salary');
        $totalBonus = (float) $salaries->sum('bonus');
        $totalDeductions = (float) $salaries->sum('deductions');

        $totalPayableGross = $totalWorkEarned + $totalBonus - $totalDeductions;
        $dueBalance = $totalPayableGross - $totalDisbursed;

        $stats = [
            'total_disbursed' => $totalDisbursed,
            'total_basic'     => (float) $salaries->sum('basic_salary'),
            'total_bonus'     => $totalBonus,
            'total_deductions'=> $totalDeductions,
            'pending_salary'  => (float) $salaries->where('status', '!=', 'paid')->sum('net_salary'),
            'total_orders'    => $totalAssignedOrders,
            'completed_orders'=> $orders->whereIn('status', ['completed', 'delivered'])->count(),
            'total_suits'     => $totalSuits,
            'completed_suits' => $completedSuits,
            'total_work_earned' => $totalWorkEarned,
            'assigned_work_amount' => $assignedWorkAmount,
            'rate'            => $rate,
            'due_balance'     => $dueBalance,
            'balance_payable' => $dueBalance,
            'present_days'    => $attendances->where('status', 'present')->count(),
            'absent_days'     => $attendances->where('status', 'absent')->count(),
            'late_days'       => $attendances->where('status', 'late')->count(),
            'leave_days'      => $attendances->where('status', 'leave')->count(),
            'half_days'       => $attendances->where('status', 'half_day')->count(),
        ];

        return view('employee_ledger.print', compact('employee', 'salaries', 'orders', 'attendances', 'stats', 'fromDate', 'toDate'));
    }

    protected function renderLedger(Employee $employee, $fromDate, $toDate, $activeTab, $allEmployees)
    {
        // 1. Salaries
        $salariesQuery = Salary::with('account')
            ->where('employee_id', $employee->id);

        if ($fromDate) {
            $salariesQuery->whereDate('payment_date', '>=', $fromDate);
        }
        if ($toDate) {
            $salariesQuery->whereDate('payment_date', '<=', $toDate);
        }

        $salaries = $salariesQuery->orderByDesc('payment_date')->get();

        // 2. Orders (Cutting, Stitching, Assigned)
        $ordersQuery = Order::with(['customer', 'service'])
            ->where(function ($q) use ($employee) {
                $q->where('assigned_employee_id', $employee->id)
                  ->orWhere('cutting_employee_id', $employee->id)
                  ->orWhere('stitching_employee_id', $employee->id);
            });

        if ($fromDate) {
            $ordersQuery->whereDate('order_date', '>=', $fromDate);
        }
        if ($toDate) {
            $ordersQuery->whereDate('order_date', '<=', $toDate);
        }

        $orders = $ordersQuery->orderByDesc('created_at')->get();

        // 3. Attendances
        $attendancesQuery = Attendance::where('employee_id', $employee->id);

        if ($fromDate) {
            $attendancesQuery->whereDate('date', '>=', $fromDate);
        }
        if ($toDate) {
            $attendancesQuery->whereDate('date', '<=', $toDate);
        }

        $attendances = $attendancesQuery->orderByDesc('date')->get();

        // Aggregate statistics
        $completedSuits = (int) $orders->whereIn('status', ['completed', 'delivered'])->sum('quantity');
        $totalSuits = (int) $orders->sum('quantity');
        $rate = (float) $employee->salary;
        $totalAssignedOrders = $orders->count();
        $totalWorkQuantity = $totalSuits > 0 ? $totalSuits : $totalAssignedOrders;

        $assignedWorkAmount = $employee->salary_type === 'project' 
            ? ($totalWorkQuantity * $rate) 
            : (float) $orders->sum(fn($o) => (float)$o->price * (int)$o->quantity);

        $totalWorkEarned = $employee->salary_type === 'project' 
            ? $assignedWorkAmount 
            : (float) $salaries->sum('basic_salary');

        $totalDisbursed = (float) $salaries->where('status', 'paid')->sum('net_salary');
        $totalBonus = (float) $salaries->sum('bonus');
        $totalDeductions = (float) $salaries->sum('deductions');

        $totalPayableGross = $totalWorkEarned + $totalBonus - $totalDeductions;
        $dueBalance = $totalPayableGross - $totalDisbursed;

        $stats = [
            'total_disbursed' => $totalDisbursed,
            'total_basic'     => (float) $salaries->sum('basic_salary'),
            'total_bonus'     => $totalBonus,
            'total_deductions'=> $totalDeductions,
            'pending_salary'  => (float) $salaries->where('status', '!=', 'paid')->sum('net_salary'),
            'total_orders'    => $totalAssignedOrders,
            'completed_orders'=> $orders->whereIn('status', ['completed', 'delivered'])->count(),
            'total_suits'     => $totalSuits,
            'completed_suits' => $completedSuits,
            'total_work_earned' => $totalWorkEarned,
            'assigned_work_amount' => $assignedWorkAmount,
            'rate'            => $rate,
            'due_balance'     => $dueBalance,
            'balance_payable' => $dueBalance,
            'present_days'    => $attendances->where('status', 'present')->count(),
            'absent_days'     => $attendances->where('status', 'absent')->count(),
            'late_days'       => $attendances->where('status', 'late')->count(),
            'leave_days'      => $attendances->where('status', 'leave')->count(),
            'half_days'       => $attendances->where('status', 'half_day')->count(),
        ];

        // Consolidated ledger entries
        $ledgerEntries = collect();

        foreach ($salaries as $sal) {
            $ledgerEntries->push([
                'type' => 'salary',
                'date' => $sal->payment_date ? Carbon::parse($sal->payment_date) : $sal->created_at,
                'title' => 'Salary Payment (' . ($sal->salary_month ?? Carbon::parse($sal->payment_date)->format('M Y')) . ')',
                'description' => $sal->notes ?: 'Disbursed via ' . ucfirst($sal->payment_method ?? 'cash') . ($sal->account ? ' (' . $sal->account->name . ')' : ''),
                'badge' => 'Payment Disbursed',
                'badge_color' => 'emerald',
                'debit' => (float) $sal->net_salary,
                'credit' => 0,
                'bonus' => (float) $sal->bonus,
                'deduction' => (float) $sal->deductions,
                'status' => $sal->status,
                'ref_id' => '#' . $sal->id,
                'raw' => $sal,
            ]);
        }

        foreach ($orders as $ord) {
            $roleLabel = [];
            if ($ord->cutting_employee_id == $employee->id) $roleLabel[] = 'Cutting';
            if ($ord->stitching_employee_id == $employee->id) $roleLabel[] = 'Stitching';
            if ($ord->assigned_employee_id == $employee->id && empty($roleLabel)) $roleLabel[] = 'General';

            $orderEarned = $employee->salary_type === 'project' 
                ? ((int)($ord->quantity ?: 1) * $rate) 
                : 0;

            $ledgerEntries->push([
                'type' => 'order',
                'date' => $ord->order_date ? Carbon::parse($ord->order_date) : $ord->created_at,
                'title' => 'Assigned Order #' . str_pad($ord->id, 4, '0', STR_PAD_LEFT) . ' (' . implode(', ', $roleLabel) . ')',
                'description' => ($ord->service?->name ?? 'Order Item') . ' - Customer: ' . ($ord->customer?->name ?? 'N/A') . ' (Qty: ' . $ord->quantity . ')',
                'badge' => 'Order ' . ucfirst($ord->status),
                'badge_color' => in_array($ord->status, ['completed', 'delivered']) ? 'emerald' : 'indigo',
                'debit' => 0,
                'credit' => $orderEarned,
                'bonus' => 0,
                'deduction' => 0,
                'status' => $ord->status,
                'ref_id' => '#' . str_pad($ord->id, 4, '0', STR_PAD_LEFT),
                'raw' => $ord,
            ]);
        }

        $ledgerEntries = $ledgerEntries->sortByDesc('date')->values();

        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('employee_ledger.index', compact(
            'employee',
            'allEmployees',
            'salaries',
            'orders',
            'attendances',
            'stats',
            'ledgerEntries',
            'fromDate',
            'toDate',
            'activeTab',
            'accounts'
        ));
    }
}
