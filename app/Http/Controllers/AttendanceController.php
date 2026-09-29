<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'daily');
        $date = $request->query('date', Carbon::today()->format('Y-m-d'));
        $search = trim($request->query('search', ''));
        $departmentId = $request->query('department_id', '');

        // --- Daily Attendance Data ---
        $employeesQuery = Employee::with(['department', 'designation', 'attendances' => function ($q) use ($date) {
            $q->whereDate('date', $date);
        }])
        ->when($departmentId !== '', function ($query) use ($departmentId) {
            return $query->where('department_id', $departmentId);
        })
        ->when($search !== '', function ($query) use ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        })
        ->latest();

        $employees = $employeesQuery->paginate(10, ['*'], 'daily_page')->withQueryString();

        // Calculate stats for the selected date
        $totalStaff = Employee::count();
        $dateAttendances = Attendance::whereDate('date', $date)->get();
        $presentCount = $dateAttendances->where('status', 'present')->count();
        $absentCount = $dateAttendances->where('status', 'absent')->count();
        $lateCount = $dateAttendances->where('status', 'late')->count();
        $halfDayCount = $dateAttendances->where('status', 'half_day')->count();
        $leaveCount = $dateAttendances->where('status', 'leave')->count();
        $unmarkedCount = max(0, $totalStaff - $dateAttendances->count());

        // --- Staff-Wise History Data ---
        $historyEmployeeId = $request->query('history_employee_id', '');
        $historyFromDate = $request->query('history_from_date', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $historyToDate = $request->query('history_to_date', Carbon::today()->format('Y-m-d'));
        $historyDepartmentId = $request->query('history_department_id', '');
        $historySearch = trim($request->query('history_search', ''));

        $historyBaseQuery = Attendance::with(['employee.department', 'employee.designation'])
            ->when($historyFromDate !== '', function ($q) use ($historyFromDate) {
                $q->whereDate('date', '>=', $historyFromDate);
            })
            ->when($historyToDate !== '', function ($q) use ($historyToDate) {
                $q->whereDate('date', '<=', $historyToDate);
            })
            ->when($historyEmployeeId !== '', function ($q) use ($historyEmployeeId) {
                $q->where('employee_id', $historyEmployeeId);
            })
            ->when($historyDepartmentId !== '', function ($q) use ($historyDepartmentId) {
                $q->whereHas('employee', function ($eq) use ($historyDepartmentId) {
                    $eq->where('department_id', $historyDepartmentId);
                });
            })
            ->when($historySearch !== '', function ($q) use ($historySearch) {
                $q->whereHas('employee', function ($eq) use ($historySearch) {
                    $eq->where('name', 'like', "%{$historySearch}%")
                       ->orWhere('phone', 'like', "%{$historySearch}%");
                });
            })
            ->orderBy('date', 'desc')
            ->latest('id');

        $allHistoryRecords = (clone $historyBaseQuery)->get();
        $historyTotal = $allHistoryRecords->count();
        $historyPresentCount = $allHistoryRecords->where('status', 'present')->count();
        $historyAbsentCount = $allHistoryRecords->where('status', 'absent')->count();
        $historyLateCount = $allHistoryRecords->where('status', 'late')->count();
        $historyHalfDayCount = $allHistoryRecords->where('status', 'half_day')->count();
        $historyLeaveCount = $allHistoryRecords->where('status', 'leave')->count();

        $historyAttendances = $historyBaseQuery->paginate(10, ['*'], 'history_page')->withQueryString();

        $departments = Department::all();
        $allEmployees = Employee::orderBy('name')->get();

        return view('attendances.index', compact(
            'tab',
            'employees',
            'date',
            'search',
            'departmentId',
            'departments',
            'allEmployees',
            'totalStaff',
            'presentCount',
            'absentCount',
            'lateCount',
            'halfDayCount',
            'leaveCount',
            'unmarkedCount',
            // History tab variables
            'historyEmployeeId',
            'historyFromDate',
            'historyToDate',
            'historyDepartmentId',
            'historySearch',
            'historyAttendances',
            'historyTotal',
            'historyPresentCount',
            'historyAbsentCount',
            'historyLateCount',
            'historyHalfDayCount',
            'historyLeaveCount'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day,leave',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date' => $request->date,
            ],
            [
                'status' => $request->status,
                'check_in' => $request->check_in ? Carbon::parse($request->check_in)->format('H:i:s') : null,
                'check_out' => $request->check_out ? Carbon::parse($request->check_out)->format('H:i:s') : null,
                'notes' => $request->notes,
            ]
        );

        return redirect()->back()->with('success', 'Attendance marked successfully!');
    }

    public function bulkMark(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day,leave',
        ]);

        $date = $request->date;
        $status = $request->status;
        $employees = Employee::all();

        foreach ($employees as $employee) {
            Attendance::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'date' => $date,
                ],
                [
                    'status' => $status,
                    'check_in' => in_array($status, ['present', 'late', 'half_day']) ? '09:00:00' : null,
                ]
            );
        }

        return redirect()->back()->with('success', "All employees marked as " . ucfirst(str_replace('_', ' ', $status)) . " for " . Carbon::parse($date)->format('M d, Y') . "!");
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:present,absent,late,half_day,leave',
            'check_in' => 'nullable|string',
            'check_out' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'check_in' => $request->check_in ? Carbon::parse($request->check_in)->format('H:i:s') : null,
            'check_out' => $request->check_out ? Carbon::parse($request->check_out)->format('H:i:s') : null,
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Attendance record updated successfully!');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->back()->with('success', 'Attendance record reset successfully!');
    }
}
