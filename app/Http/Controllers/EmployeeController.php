<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $employees = Employee::with(['department', 'designation'])
            ->when($search !== '', function ($query) use ($search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereHas('department', function ($dq) use ($search) {
                          $dq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('designation', function ($dgq) use ($search) {
                          $dgq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $departments = Department::all();
        $designations = Designation::all();

        return view('employees.index', compact('employees', 'departments', 'designations', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:monthly,weekly,project',
            'joining_date' => 'nullable|date',
        ]);

        Employee::create($request->only('name', 'email', 'phone', 'department_id', 'designation_id', 'salary', 'salary_type', 'joining_date'));

        return redirect()->route('employees.index')->with('success', 'Employee added successfully!');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'salary' => 'nullable|numeric|min:0',
            'salary_type' => 'required|in:monthly,weekly,project',
            'joining_date' => 'nullable|date',
        ]);

        $employee->update($request->only('name', 'email', 'phone', 'department_id', 'designation_id', 'salary', 'salary_type', 'joining_date'));

        return redirect()->route('employees.index')->with('success', 'Employee details updated successfully!');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }
}
