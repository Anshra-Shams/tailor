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
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $activeTab = $request->query('tab', 'all'); // 'all', 'salaries', 'orders', 'attendance'

        $allEmployees = Employee::with(['department', 'designation'])
            ->orderBy('name')
            ->get();

        // If no employee selected, show overview directory
        if (!$employeeId) {
            $stats = [
                'total_employees' => Employee::count(),
                'active_employees' => Employee::where('is_active', true)->count(),
                'total_paid' => (float) Salary::where('status', 'paid')->sum('net_salary'),
                'total_bonus' => (float) Salary::where('status', 'paid')->sum('bonus'),
                'total_deductions' => (float) Salary::where('status', 'paid')->sum('deductions'),
            ];

            // Load aggregate data for each employee
            $employees = Employee::with(['department', 'designation'])
                ->withCount([
                    'salaries as total_payments_count',
                    'attendances as total_attendances_count',
                ])
                ->withSum(['salaries as total_disbursed' => function ($q) {
                    $q->where('status', 'paid');
                }], 'net_salary')
                ->withSum(['salaries as total_deductions' => function ($q) {
                    $q->where('status', 'paid');
                }], 'deductions')
                ->withSum(['salaries as total_bonus' => function ($q) {
                    $q->where('status', 'paid');
                }], 'bonus')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            return view('employee_ledger.index', compact(
                'allEmployees',
                'employees',
                'stats',
                'employeeId',
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
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
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

        $completedSuits = (int) $orders->where('status', 'completed')->sum('quantity');
        $totalSuits = (int) $orders->sum('quantity');
        $rate = (float) $employee->salary;
        $totalWorkEarned = $employee->salary_type === 'project' 
            ? ($completedSuits * $rate) 
            : (float) $salaries->sum('basic_salary');

        $stats = [
            'total_disbursed' => (float) $salaries->where('status', 'paid')->sum('net_salary'),
            'total_basic'     => (float) $salaries->sum('basic_salary'),
            'total_bonus'     => (float) $salaries->sum('bonus'),
            'total_deductions'=> (float) $salaries->sum('deductions'),
            'pending_salary'  => (float) $salaries->where('status', '!=', 'paid')->sum('net_salary'),
            'total_orders'    => $orders->count(),
            'completed_orders'=> $orders->where('status', 'completed')->count(),
            'total_suits'     => $totalSuits,
            'completed_suits' => $completedSuits,
            'total_work_earned' => $totalWorkEarned,
            'assigned_work_amount' => $employee->salary_type === 'project' ? (($totalSuits > 0 ? $totalSuits : $orders->count()) * $rate) : (float) $orders->sum(fn($o) => (float)$o->price * (int)$o->quantity),
            'rate'            => $rate,
            'balance_payable' => max(0, $totalWorkEarned - (float) $salaries->where('status', 'paid')->sum('net_salary')),
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
        $completedSuits = (int) $orders->where('status', 'completed')->sum('quantity');
        $totalSuits = (int) $orders->sum('quantity');
        $rate = (float) $employee->salary;
        $totalWorkEarned = $employee->salary_type === 'project' 
            ? ($completedSuits * $rate) 
            : (float) $salaries->sum('basic_salary');

        $stats = [
            'total_disbursed' => (float) $salaries->where('status', 'paid')->sum('net_salary'),
            'total_basic'     => (float) $salaries->sum('basic_salary'),
            'total_bonus'     => (float) $salaries->sum('bonus'),
            'total_deductions'=> (float) $salaries->sum('deductions'),
            'pending_salary'  => (float) $salaries->where('status', '!=', 'paid')->sum('net_salary'),
            'total_orders'    => $orders->count(),
            'completed_orders'=> $orders->where('status', 'completed')->count(),
            'total_suits'     => $totalSuits,
            'completed_suits' => $completedSuits,
            'total_work_earned' => $totalWorkEarned,
            'assigned_work_amount' => $employee->salary_type === 'project' ? (($totalSuits > 0 ? $totalSuits : $orders->count()) * $rate) : (float) $orders->sum(fn($o) => (float)$o->price * (int)$o->quantity),
            'rate'            => $rate,
            'balance_payable' => max(0, $totalWorkEarned - (float) $salaries->where('status', 'paid')->sum('net_salary')),
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
                'debit' => (float) $sal->net_salary, // Company paid out to employee
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

            $ledgerEntries->push([
                'type' => 'order',
                'date' => $ord->order_date ? Carbon::parse($ord->order_date) : $ord->created_at,
                'title' => 'Assigned Order #' . str_pad($ord->id, 4, '0', STR_PAD_LEFT) . ' (' . implode(', ', $roleLabel) . ')',
                'description' => ($ord->service?->name ?? 'Order Item') . ' - Customer: ' . ($ord->customer?->name ?? 'N/A') . ' (Qty: ' . $ord->quantity . ')',
                'badge' => 'Order ' . ucfirst($ord->status),
                'badge_color' => $ord->status === 'completed' ? 'indigo' : 'amber',
                'debit' => 0,
                'credit' => 0, // Recorded for tracking
                'bonus' => 0,
                'deduction' => 0,
                'status' => $ord->status,
                'ref_id' => '#' . str_pad($ord->id, 4, '0', STR_PAD_LEFT),
                'raw' => $ord,
            ]);
        }

        $ledgerEntries = $ledgerEntries->sortByDesc('date')->values();

        $accounts = Account::where('is_active', true)->orderBy('name')->get();

        return view('employee_ledger.show', compact(
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
