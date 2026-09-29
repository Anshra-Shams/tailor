<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Salary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', Carbon::today()->format('Y-m'));
        $departmentId = $request->query('department_id', '');
        $statusFilter = $request->query('status', ''); // '', 'paid', 'unpaid'
        $search = trim($request->query('search', ''));

        // Query active employees with their salary record for selected month
        $employeesQuery = Employee::with([
            'department',
            'designation',
            'salaries' => function ($q) use ($month) {
                $q->where('salary_month', $month);
            }
        ])
        ->where('is_active', true)
        ->when($departmentId !== '', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        // If filtering by paid/unpaid status
        if ($statusFilter === 'paid') {
            $employeesQuery->whereHas('salaries', function ($q) use ($month) {
                $q->where('salary_month', $month)->where('status', 'paid');
            });
        } elseif ($statusFilter === 'unpaid') {
            $employeesQuery->whereDoesntHave('salaries', function ($q) use ($month) {
                $q->where('salary_month', $month)->where('status', 'paid');
            });
        }

        $employees = $employeesQuery->orderBy('name')->paginate(12)->withQueryString();

        // Calculate Monthly Statistics
        $totalActiveEmployees = Employee::where('is_active', true)->count();
        $monthlySalaries = Salary::where('salary_month', $month)->get();

        $totalPaidAmount = $monthlySalaries->where('status', 'paid')->sum('net_salary');
        $paidStaffCount = $monthlySalaries->where('status', 'paid')->unique('employee_id')->count();
        $unpaidStaffCount = max(0, $totalActiveEmployees - $paidStaffCount);
        $totalBonusPaid = $monthlySalaries->where('status', 'paid')->sum('bonus');
        $totalDeductions = $monthlySalaries->where('status', 'paid')->sum('deductions');

        $departments = Department::orderBy('name')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $allEmployees = Employee::with(['department', 'designation'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('salaries.index', compact(
            'employees',
            'month',
            'departmentId',
            'statusFilter',
            'search',
            'totalActiveEmployees',
            'totalPaidAmount',
            'paidStaffCount',
            'unpaidStaffCount',
            'totalBonusPaid',
            'totalDeductions',
            'departments',
            'accounts',
            'allEmployees'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'salary_month'   => 'required|string|max:7',
            'payment_date'   => 'required|date',
            'basic_salary'   => 'required|numeric|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deductions'     => 'nullable|numeric|min:0',
            'net_salary'     => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
            'account_id'     => 'nullable|exists:accounts,id',
            'status'         => 'required|in:paid,pending,partial',
            'notes'          => 'nullable|string|max:500',
        ]);

        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;

        DB::transaction(function () use ($validated) {
            $salary = Salary::updateOrCreate(
                [
                    'employee_id'  => $validated['employee_id'],
                    'salary_month' => $validated['salary_month'],
                ],
                $validated
            );

            // If linked to an account and paid, update account balance
            if (!empty($validated['account_id']) && $validated['status'] === 'paid') {
                $account = Account::find($validated['account_id']);
                if ($account) {
                    $account->decrement('current_balance', $validated['net_salary']);
                }
            }
        });

        return redirect()->back()->with('success', 'Salary payment processed successfully.');
    }

    public function destroy(Salary $salary)
    {
        DB::transaction(function () use ($salary) {
            // Refund account balance if it was deducted
            if ($salary->account_id && $salary->status === 'paid') {
                $account = Account::find($salary->account_id);
                if ($account) {
                    $account->increment('current_balance', $salary->net_salary);
                }
            }
            $salary->delete();
        });

        return redirect()->back()->with('success', 'Salary payment record deleted successfully.');
    }
}
