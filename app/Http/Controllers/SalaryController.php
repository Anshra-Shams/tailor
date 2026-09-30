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
        $fromDate = $request->query('from_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->query('to_date', Carbon::today()->format('Y-m-d'));
        $departmentId = $request->query('department_id', '');
        $salaryType = $request->query('salary_type', ''); // '', 'monthly', 'weekly', 'project'
        $statusFilter = $request->query('status', ''); // '', 'paid', 'unpaid'
        $search = trim($request->query('search', ''));

        // Query active employees with their salary record for selected date range
        $employeesQuery = Employee::with([
            'department',
            'designation',
            'salaries' => function ($q) use ($fromDate, $toDate) {
                $q->where(function ($sub) use ($fromDate, $toDate) {
                    $sub->where(function ($inner) use ($fromDate, $toDate) {
                        $inner->whereDate('from_date', '>=', $fromDate)
                              ->whereDate('to_date', '<=', $toDate);
                    })->orWhereBetween('payment_date', [$fromDate, $toDate]);
                })->latest('id');
            }
        ])
        ->where('is_active', true)
        ->when($departmentId !== '', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        })
        ->when($salaryType !== '', function ($q) use ($salaryType) {
            $q->where('salary_type', $salaryType);
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
            $employeesQuery->whereHas('salaries', function ($q) use ($fromDate, $toDate) {
                $q->where(function ($sub) use ($fromDate, $toDate) {
                    $sub->where(function ($inner) use ($fromDate, $toDate) {
                        $inner->whereDate('from_date', '>=', $fromDate)
                              ->whereDate('to_date', '<=', $toDate);
                    })->orWhereBetween('payment_date', [$fromDate, $toDate]);
                })->where('status', 'paid');
            });
        } elseif ($statusFilter === 'unpaid') {
            $employeesQuery->whereDoesntHave('salaries', function ($q) use ($fromDate, $toDate) {
                $q->where(function ($sub) use ($fromDate, $toDate) {
                    $sub->where(function ($inner) use ($fromDate, $toDate) {
                        $inner->whereDate('from_date', '>=', $fromDate)
                              ->whereDate('to_date', '<=', $toDate);
                    })->orWhereBetween('payment_date', [$fromDate, $toDate]);
                })->where('status', 'paid');
            });
        }

        $employees = $employeesQuery->orderBy('name')->paginate(12)->withQueryString();

        // Calculate Statistics for Selected Date Range
        $totalActiveEmployees = Employee::where('is_active', true)->count();
        $rangeSalaries = Salary::where(function ($sub) use ($fromDate, $toDate) {
            $sub->where(function ($inner) use ($fromDate, $toDate) {
                $inner->whereDate('from_date', '>=', $fromDate)
                      ->whereDate('to_date', '<=', $toDate);
            })->orWhereBetween('payment_date', [$fromDate, $toDate]);
        })->get();

        $totalPaidAmount = $rangeSalaries->where('status', 'paid')->sum('net_salary');
        $paidStaffCount = $rangeSalaries->where('status', 'paid')->unique('employee_id')->count();
        $unpaidStaffCount = max(0, $totalActiveEmployees - $paidStaffCount);
        $totalBonusPaid = $rangeSalaries->where('status', 'paid')->sum('bonus');
        $totalDeductions = $rangeSalaries->where('status', 'paid')->sum('deductions');

        $departments = Department::orderBy('name')->get();
        $accounts = Account::where('is_active', true)->orderBy('name')->get();
        $allEmployees = Employee::with(['department', 'designation'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('salaries.index', compact(
            'employees',
            'fromDate',
            'toDate',
            'departmentId',
            'salaryType',
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
            'salary_id'      => 'nullable|exists:salaries,id',
            'employee_id'    => 'required|exists:employees,id',
            'from_date'      => 'required|date',
            'to_date'        => 'required|date',
            'payment_date'   => 'required|date',
            'basic_salary'   => 'required|numeric|min:0',
            'bonus'          => 'nullable|numeric|min:0',
            'deductions'     => 'nullable|numeric|min:0',
            'net_salary'     => 'required|numeric|min:0',
            'account_id'     => 'nullable|exists:accounts,id',
            'status'         => 'required|in:paid,pending,partial',
            'notes'          => 'nullable|string|max:500',
        ]);

        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['deductions'] = $validated['deductions'] ?? 0;
        $validated['salary_month'] = Carbon::parse($validated['from_date'])->format('Y-m');
        $validated['payment_method'] = 'cash';

        DB::transaction(function () use ($validated, $request) {
            if (!empty($request->salary_id)) {
                $salary = Salary::findOrFail($request->salary_id);
                $salary->update($validated);
            } else {
                $salary = Salary::create($validated);
            }

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
