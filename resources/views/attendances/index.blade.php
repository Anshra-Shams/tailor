@extends('layouts.app')

@section('title', 'Staff Attendance')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Staff Attendance</h2>
            <p class="text-sm text-slate-500 mt-1">
                @if($tab === 'history')
                    Historical attendance logs and staff monthly records
                @else
                    Daily attendance sheet for <span class="font-semibold text-indigo-600">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</span>
                @endif
            </p>
        </div>
        
        @if($tab !== 'history')
            <div class="flex items-center gap-3">
                <!-- Quick Bulk Mark Present Form -->
                <form action="{{ route('attendances.bulkMark') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="date" value="{{ $date }}">
                    <input type="hidden" name="status" value="present">
                    <button type="submit" 
                            onclick="return confirm('Mark ALL employees as Present for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}?')"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-600/20 transition-all duration-200 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Mark All Present
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200/80">
        <a href="{{ route('attendances.index', ['tab' => 'daily', 'date' => $date]) }}" 
           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 transition-all {{ $tab !== 'history' ? 'border-emerald-600 text-emerald-600 bg-emerald-50/50 rounded-t-xl shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-t-xl' }}">
            <svg class="w-4 h-4 {{ $tab !== 'history' ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Daily Attendance
        </a>
        
        <a href="{{ route('attendances.index', ['tab' => 'history']) }}" 
           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold border-b-2 transition-all {{ $tab === 'history' ? 'border-indigo-600 text-indigo-600 bg-indigo-50/50 rounded-t-xl shadow-sm' : 'border-transparent text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-t-xl' }}">
            <svg class="w-4 h-4 {{ $tab === 'history' ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            Staff-Wise History
        </a>
    </div>

    @if($tab !== 'history')
        <!-- ========================================== -->
        <!-- TAB 1: DAILY ATTENDANCE                   -->
        <!-- ========================================== -->

        <!-- Stats Cards (Single Row) -->
        <div class="grid grid-cols-4 gap-4">
            <!-- Present Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Present</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $presentCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Absent Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Absent</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $absentCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Late / Half Day Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Late / Half Day</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $lateCount + $halfDayCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Leave / Unmarked Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">On Leave / Pending</p>
                    <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $leaveCount + $unmarkedCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar (Strict Single Row) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-sm">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex items-center gap-3">
                <input type="hidden" name="tab" value="daily">
                
                <!-- Date Picker -->
                <div class="w-44 shrink-0">
                    <input type="date" 
                           name="date" 
                           value="{{ $date }}" 
                           title="Select Date"
                           class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2.5 transition font-medium">
                </div>

                <!-- Department Filter -->
                <div class="w-48 shrink-0">
                    <select name="department_id" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2.5 transition">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Field -->
                <div class="flex-1 min-w-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Search employee by name, phone..." 
                               class="block w-full pl-9 pr-8 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        @if(!empty($search))
                            <a href="{{ route('attendances.index', ['tab' => 'daily', 'date' => $date, 'department_id' => $departmentId]) }}" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter & Clear Buttons -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-500/20 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    @if(!empty($search) || !empty($departmentId) || $date !== \Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ route('attendances.index', ['tab' => 'daily']) }}" 
                           class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-xl transition" 
                           title="Reset all filters">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold uppercase text-slate-500 tracking-wider">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Check In</th>
                            <th class="px-6 py-4">Check Out</th>
                            <th class="px-6 py-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($employees as $employee)
                            @php
                                $att = $employee->attendances->first();
                                $status = $att ? $att->status : null;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-400">
                                    {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                                </td>
                                
                                <!-- Employee Details -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            {{ substr($employee->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ $employee->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $employee->designation ? $employee->designation->name : 'Staff Member' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Department -->
                                <td class="px-6 py-4">
                                    @if($employee->department)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $employee->department->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Unassigned</span>
                                    @endif
                                </td>

                                <!-- Status Dropdown -->
                                <td class="px-6 py-4">
                                    <form action="{{ route('attendances.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">
                                        <input type="hidden" name="check_in" value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}">
                                        <input type="hidden" name="check_out" value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}">
                                        <input type="hidden" name="notes" value="{{ $att ? $att->notes : '' }}">
                                        <select name="status" 
                                                onchange="this.form.submit()" 
                                                class="text-xs font-semibold rounded-xl border py-1.5 px-3 transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/20
                                                @if($status === 'present') bg-emerald-50 text-emerald-700 border-emerald-200 focus:border-emerald-500
                                                @elseif($status === 'absent') bg-rose-50 text-rose-700 border-rose-200 focus:border-rose-500
                                                @elseif($status === 'late') bg-amber-50 text-amber-700 border-amber-200 focus:border-amber-500
                                                @elseif($status === 'half_day') bg-blue-50 text-blue-700 border-blue-200 focus:border-blue-500
                                                @elseif($status === 'leave') bg-purple-50 text-purple-700 border-purple-200 focus:border-purple-500
                                                @else bg-slate-50 text-slate-500 border-slate-200 focus:border-indigo-500 @endif">
                                            <option value="" disabled {{ !$status ? 'selected' : '' }}>-- Select Status --</option>
                                            <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="late" {{ $status === 'late' ? 'selected' : '' }}>Late</option>
                                            <option value="half_day" {{ $status === 'half_day' ? 'selected' : '' }}>Half Day</option>
                                            <option value="leave" {{ $status === 'leave' ? 'selected' : '' }}>On Leave</option>
                                        </select>
                                    </form>
                                </td>

                                <!-- Check In Input -->
                                <td class="px-6 py-4">
                                    <form action="{{ route('attendances.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">
                                        <input type="hidden" name="status" value="{{ $status ?? 'present' }}">
                                        <input type="hidden" name="check_out" value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}">
                                        <input type="hidden" name="notes" value="{{ $att ? $att->notes : '' }}">
                                        <input type="time" 
                                               name="check_in" 
                                               value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}"
                                               onchange="this.form.submit()"
                                               class="bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-800 px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium">
                                    </form>
                                </td>

                                <!-- Check Out Input -->
                                <td class="px-6 py-4">
                                    <form action="{{ route('attendances.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">
                                        <input type="hidden" name="status" value="{{ $status ?? 'present' }}">
                                        <input type="hidden" name="check_in" value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}">
                                        <input type="hidden" name="notes" value="{{ $att ? $att->notes : '' }}">
                                        <input type="time" 
                                               name="check_out" 
                                               value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}"
                                               onchange="this.form.submit()"
                                               class="bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-800 px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium">
                                    </form>
                                </td>

                                <!-- Remarks Input -->
                                <td class="px-6 py-4">
                                    <form action="{{ route('attendances.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                        <input type="hidden" name="date" value="{{ $date }}">
                                        <input type="hidden" name="status" value="{{ $status ?? 'present' }}">
                                        <input type="hidden" name="check_in" value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}">
                                        <input type="hidden" name="check_out" value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}">
                                        <input type="text" 
                                               name="notes" 
                                               value="{{ $att ? $att->notes : '' }}" 
                                               placeholder="Optional"
                                               onchange="this.form.submit()"
                                               class="w-full min-w-[150px] bg-slate-50/70 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 px-3 py-1.5 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium">
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    @if(!empty($search))
                                        No staff match "{{ $search }}". <a href="{{ route('attendances.index', ['tab' => 'daily', 'date' => $date]) }}" class="text-indigo-600 underline">Clear search</a>
                                    @else
                                        No staff members found.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Table Pagination Footer -->
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-slate-500 font-medium">
                    Showing <span class="font-semibold text-slate-700">{{ $employees->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-700">{{ $employees->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $employees->total() }}</span> staff members (10 per page)
                </p>
                <div>
                    {{ $employees->links() }}
                </div>
            </div>
        </div>

    @else
        <!-- ========================================== -->
        <!-- TAB 2: STAFF-WISE HISTORY                 -->
        <!-- ========================================== -->

        <!-- History Stats Cards (Single Row) -->
        <div class="grid grid-cols-4 gap-4">
            <!-- Total Logs Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Total Logs</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $historyTotal }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>

            <!-- Present Days Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Present Days</p>
                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $historyPresentCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Absent Days Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Absent Days</p>
                    <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $historyAbsentCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Late / Half Day / Leaves Card -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Late / Leaves</p>
                    <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $historyLateCount + $historyHalfDayCount + $historyLeaveCount }}</h3>
                </div>
                <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- History Filter Bar (Strict Single Row with Labels) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm">
            <form method="GET" action="{{ route('attendances.index') }}" class="flex items-end gap-3 flex-nowrap overflow-x-auto pb-1 sm:pb-0">
                <input type="hidden" name="tab" value="history">

                <!-- From Date -->
                <div class="w-40 shrink-0">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">From Date</label>
                    <input type="date" 
                           name="history_from_date" 
                           value="{{ $historyFromDate }}" 
                           class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                </div>

                <!-- To Date -->
                <div class="w-40 shrink-0">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">To Date</label>
                    <input type="date" 
                           name="history_to_date" 
                           value="{{ $historyToDate }}" 
                           class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                </div>

                <!-- Select Staff Member -->
                <div class="w-64 shrink-0">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Staff Member</label>
                    <select name="history_employee_id" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                        <option value="">All Staff Members</option>
                        @foreach($allEmployees as $emp)
                            <option value="{{ $emp->id }}" {{ $historyEmployeeId == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Department Filter -->
                <div class="w-48 shrink-0">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department</label>
                    <select name="history_department_id" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $historyDepartmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Field (Compact, No top label) -->
                <div class="w-56 shrink-0">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="history_search" 
                               value="{{ $historySearch }}" 
                               placeholder="Search staff..." 
                               class="block w-full pl-10 pr-8 py-2.5 h-[42px] bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                        @if(!empty($historySearch))
                            <a href="{{ route('attendances.index', ['tab' => 'history', 'history_from_date' => $historyFromDate, 'history_to_date' => $historyToDate, 'history_employee_id' => $historyEmployeeId, 'history_department_id' => $historyDepartmentId]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Filter & Reset Buttons -->
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" 
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/35 transition-all duration-200 h-[42px] cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                    </button>
                    @if(!empty($historySearch) || !empty($historyEmployeeId) || !empty($historyDepartmentId) || $historyFromDate !== \Carbon\Carbon::today()->startOfMonth()->format('Y-m-d') || $historyToDate !== \Carbon\Carbon::today()->format('Y-m-d'))
                        <a href="{{ route('attendances.index', ['tab' => 'history']) }}" 
                           class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-sm font-medium rounded-xl transition-all duration-200 h-[42px]" 
                           title="Reset all filters">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- History Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-bold uppercase text-slate-600 tracking-wider">
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Check In</th>
                            <th class="px-6 py-4">Check Out</th>
                            <th class="px-6 py-4">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($historyAttendances as $attendance)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-400 text-sm">
                                    {{ $loop->iteration + ($historyAttendances->currentPage() - 1) * $historyAttendances->perPage() }}
                                </td>
                                
                                <!-- Date -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-500 font-medium">{{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</div>
                                </td>

                                <!-- Employee -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm shrink-0">
                                             {{ strtoupper(substr($attendance->employee->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-sm">{{ $attendance->employee->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-slate-500 font-medium">{{ $attendance->employee->designation->name ?? 'Staff' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Department -->
                                <td class="px-6 py-4">
                                    @if($attendance->employee && $attendance->employee->department)
                                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $attendance->employee->department->name }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Unassigned</span>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4">
                                    @if($attendance->status === 'present')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Present
                                        </span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> Absent
                                        </span>
                                    @elseif($attendance->status === 'late')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Late
                                        </span>
                                    @elseif($attendance->status === 'half_day')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span> Half Day
                                        </span>
                                    @elseif($attendance->status === 'leave')
                                        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span> On Leave
                                        </span>
                                    @endif
                                </td>

                                <!-- Check In -->
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                    {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '—' }}
                                </td>

                                <!-- Check Out -->
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                    {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '—' }}
                                </td>

                                <!-- Remarks -->
                                <td class="px-6 py-4 text-sm text-slate-700">
                                    {{ $attendance->notes ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-600">No attendance history found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try selecting a different date range, staff member, or clear filters.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Table Pagination Footer -->
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-slate-500 font-medium">
                    Showing <span class="font-semibold text-slate-700">{{ $historyAttendances->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-700">{{ $historyAttendances->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $historyAttendances->total() }}</span> history records (10 per page)
                </p>
                <div>
                    {{ $historyAttendances->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
