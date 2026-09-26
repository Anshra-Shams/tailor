@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Employees</h2>
            <p class="text-sm text-slate-500 mt-1">Manage shop staff, designations, and salary structures</p>
        </div>
        <button onclick="document.getElementById('createEmployeeModal').classList.remove('hidden')" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Employee
        </button>
    </div>

    <!-- Search Bar (Above Table) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('employees.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Search employee by name, phone, email, or department..." 
                       class="block w-full pl-11 pr-10 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-200 text-sm">
                @if(!empty($search))
                    <a href="{{ route('employees.index') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
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
                        <th class="px-6 py-4">Employee Name</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Designation</th>
                        <th class="px-6 py-4">Salary</th>
                        <th class="px-6 py-4">Joining Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-400">{{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                        {{ substr($employee->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $employee->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <div class="font-medium text-slate-700">{{ $employee->phone ?? '—' }}</div>
                                @if($employee->email)
                                    <div class="text-xs text-slate-400 mt-0.5">{{ $employee->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($employee->department)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $employee->department->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($employee->designation)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-100">
                                        {{ $employee->designation->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Optional (None)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-900">
                                    PKR {{ number_format($employee->salary, 2) }}
                                </div>
                                <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500 mt-0.5">
                                    @if($employee->salary_type === 'weekly')
                                        <span class="inline-flex items-center gap-1 text-amber-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Weekly
                                        </span>
                                    @elseif($employee->salary_type === 'project')
                                        <span class="inline-flex items-center gap-1 text-emerald-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Project Base
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-indigo-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span> Monthly
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600 text-xs font-medium">
                                {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') : '—' }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                        onclick="openEditEmployeeModal({{ json_encode($employee) }})"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" 
                                        title="Edit Employee">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline js-delete-form" data-name="{{ $employee->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all" title="Delete Employee">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                @if(!empty($search))
                                    No employees match "{{ $search }}". <a href="{{ route('employees.index') }}" class="text-indigo-600 underline">Clear search</a>
                                @else
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-base font-medium text-slate-600">No employees found</p>
                                    <p class="text-xs text-slate-400 mt-1">Click "Add Employee" button above to add staff members.</p>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Modal Dialog (Compact) -->
<div id="createEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden my-auto transform transition-all" style="max-width: 520px; width: 100%;">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-slate-50 to-indigo-50/40 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Add New Employee</h3>
                    <p class="text-[11px] text-slate-500">Enter staff personal and payroll details</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('createEmployeeModal').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('employees.store') }}" method="POST" class="p-5 space-y-3">
            @csrf

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Mohammad Ali" 
                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
            </div>

            <!-- Phone & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <input type="text" name="phone" placeholder="0300-1234567" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <input type="email" name="email" placeholder="ali@example.com" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
            </div>

            <!-- Department & Designation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Department <span class="text-rose-500">*</span></label>
                    <select name="department_id" required class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Designation <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <select name="designation_id" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="">-- Optional (None) --</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Salary Type & Salary Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Salary Type <span class="text-rose-500">*</span></label>
                    <select name="salary_type" required onchange="updateSalaryLabel('create', this.value)" 
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="monthly" selected>Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="project">Project Based</option>
                    </select>
                </div>
                <div>
                    <label id="create_salary_label" class="block text-xs font-semibold text-slate-700 mb-1">Monthly Salary (PKR)</label>
                    <input type="number" step="0.01" name="salary" placeholder="30000" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
            </div>

            <!-- Joining Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Joining Date <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                <input type="date" name="joining_date" 
                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createEmployeeModal').classList.add('hidden')" 
                        class="px-3.5 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-xs font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition">
                    Save Employee
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal Dialog (Compact) -->
<div id="editEmployeeModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden my-auto transform transition-all" style="max-width: 520px; width: 100%;">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-slate-50 to-indigo-50/40 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Edit Employee</h3>
                    <p class="text-[11px] text-slate-500">Update staff member information</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('editEmployeeModal').classList.add('hidden')" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form id="editEmployeeForm" method="POST" class="p-5 space-y-3">
            @csrf
            @method('PUT')

            <!-- Full Name -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_emp_name" name="name" required 
                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
            </div>

            <!-- Phone & Email -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <input type="text" id="edit_emp_phone" name="phone" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <input type="email" id="edit_emp_email" name="email" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
            </div>

            <!-- Department & Designation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Department <span class="text-rose-500">*</span></label>
                    <select id="edit_emp_department_id" name="department_id" required 
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Designation <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                    <select id="edit_emp_designation_id" name="designation_id" 
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="">-- Optional (None) --</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}">{{ $desig->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Salary Type & Salary Amount -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Salary Type <span class="text-rose-500">*</span></label>
                    <select id="edit_emp_salary_type" name="salary_type" required onchange="updateSalaryLabel('edit', this.value)" 
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                        <option value="project">Project Based</option>
                    </select>
                </div>
                <div>
                    <label id="edit_salary_label" class="block text-xs font-semibold text-slate-700 mb-1">Monthly Salary (PKR)</label>
                    <input type="number" step="0.01" id="edit_emp_salary" name="salary" 
                           class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
                </div>
            </div>

            <!-- Joining Date -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Joining Date <span class="text-slate-400 font-normal text-[10px]">(optional)</span></label>
                <input type="date" id="edit_emp_joining_date" name="joining_date" 
                       class="w-full bg-slate-50/50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3 py-2 transition">
            </div>

            <!-- Footer Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editEmployeeModal').classList.add('hidden')" 
                        class="px-3.5 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-xs font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition">
                    Update Employee
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateSalaryLabel(prefix, type) {
        const label = document.getElementById(prefix + '_salary_label');
        if (!label) return;
        if (type === 'project') {
            label.innerText = 'Project Base (PKR)';
        } else if (type === 'weekly') {
            label.innerText = 'Weekly Salary (PKR)';
        } else {
            label.innerText = 'Monthly Salary (PKR)';
        }
    }

    function openEditEmployeeModal(emp) {
        document.getElementById('editEmployeeForm').action = '/employees/' + emp.id;
        document.getElementById('edit_emp_name').value = emp.name || '';
        document.getElementById('edit_emp_phone').value = emp.phone || '';
        document.getElementById('edit_emp_email').value = emp.email || '';
        document.getElementById('edit_emp_department_id').value = emp.department_id || '';
        document.getElementById('edit_emp_designation_id').value = emp.designation_id || '';
        document.getElementById('edit_emp_salary').value = emp.salary || '';
        const salaryType = emp.salary_type || 'monthly';
        document.getElementById('edit_emp_salary_type').value = salaryType;
        updateSalaryLabel('edit', salaryType);
        document.getElementById('edit_emp_joining_date').value = emp.joining_date || '';
        document.getElementById('editEmployeeModal').classList.remove('hidden');
    }
</script>
@endsection
