@extends('layouts.app')

@section('title', 'Pay Salary & Payroll')

@section('content')
<div class="space-y-6" x-data="salaryManager()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Staff Salary & Payroll</h2>
            <p class="text-sm text-slate-500 mt-1">
                Manage monthly staff compensation, salary disbursements, bonuses, and deductions for 
                <span class="font-semibold text-indigo-600">{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}</span>
            </p>
        </div>
        
        <div class="flex items-center gap-3">
            <button @click="openPayModal()" 
                    type="button"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4.5 py-2.5 rounded-xl shadow-lg shadow-indigo-600/25 transition-all duration-200 text-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Pay Salary
            </button>
        </div>
    </div>

    <!-- Stats Overview Cards (Single Row) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Active Staff -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Total Staff</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalActiveEmployees }}</h3>
            </div>
            <div class="w-11 h-11 shrink-0 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Paid Amount This Month -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Total Disbursed ({{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M') }})</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($totalPaidAmount, 0) }}</h3>
            </div>
            <div class="w-11 h-11 shrink-0 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Paid Staff Count -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Paid Staff</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $paidStaffCount }} <span class="text-xs font-normal text-slate-400">/ {{ $totalActiveEmployees }}</span></h3>
            </div>
            <div class="w-11 h-11 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Pending / Unpaid Staff Count -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 truncate">Pending Salaries</p>
                <h3 class="text-2xl font-bold {{ $unpaidStaffCount > 0 ? 'text-amber-600' : 'text-slate-700' }} mt-1">{{ $unpaidStaffCount }}</h3>
            </div>
            <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar (Single Row with Labels) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm">
        <form method="GET" action="{{ route('salaries.index') }}" class="flex items-end gap-3 flex-nowrap overflow-x-auto pb-1 sm:pb-0">
            <!-- Salary Month -->
            <div class="w-40 shrink-0">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Salary Month</label>
                <input type="month" 
                       name="month" 
                       value="{{ $month }}" 
                       class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
            </div>

            <!-- Department Filter -->
            <div class="w-48 shrink-0">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department</label>
                <select name="department_id" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="w-40 shrink-0">
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Payment Status</label>
                <select name="status" class="w-full bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 h-[42px] transition font-medium">
                    <option value="">All Staff</option>
                    <option value="paid" {{ $statusFilter === 'paid' ? 'selected' : '' }}>Paid Only</option>
                    <option value="unpaid" {{ $statusFilter === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                </select>
            </div>

            <!-- Search Field (Compact, No top label) -->
            <div class="w-60 shrink-0">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search staff name..." 
                           class="block w-full pl-10 pr-8 py-2.5 h-[42px] bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    @if(!empty($search))
                        <a href="{{ route('salaries.index', ['month' => $month, 'department_id' => $departmentId, 'status' => $statusFilter]) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
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
                @if(!empty($search) || !empty($departmentId) || !empty($statusFilter) || $month !== \Carbon\Carbon::today()->format('Y-m'))
                    <a href="{{ route('salaries.index') }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-slate-700 text-sm font-medium rounded-xl transition-all duration-200 h-[42px]" 
                       title="Reset all filters">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Salary Sheet Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-bold uppercase text-slate-600 tracking-wider">
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Department</th>
                        <th class="px-6 py-4">Base Salary</th>
                        <th class="px-6 py-4">Bonus / Deductions</th>
                        <th class="px-6 py-4">Net Salary</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Payment Details</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($employees as $emp)
                        @php
                            $salaryRecord = $emp->salaries->first();
                            $isPaid = $salaryRecord && $salaryRecord->status === 'paid';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- # -->
                            <td class="px-6 py-4 font-semibold text-slate-400 text-sm">
                                {{ $loop->iteration + ($employees->currentPage() - 1) * $employees->perPage() }}
                            </td>

                            <!-- Employee Info -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm shrink-0">
                                         {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $emp->name }}</div>
                                        <div class="text-xs text-slate-500 font-medium">{{ $emp->designation->name ?? 'Staff Member' }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Department -->
                            <td class="px-6 py-4">
                                @if($emp->department)
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $emp->department->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs italic">Unassigned</span>
                                @endif
                            </td>

                            <!-- Base Salary -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 text-sm">Rs. {{ number_format($emp->salary ?? 0, 0) }}</div>
                                <div class="text-[11px] text-slate-400 font-medium capitalize">{{ str_replace('_', ' ', $emp->salary_type ?? 'monthly') }}</div>
                            </td>

                            <!-- Bonus & Deductions -->
                            <td class="px-6 py-4">
                                @if($salaryRecord)
                                    <div class="flex items-center gap-2 text-xs">
                                        @if($salaryRecord->bonus > 0)
                                            <span class="text-emerald-600 font-semibold">+Rs. {{ number_format($salaryRecord->bonus, 0) }}</span>
                                        @endif
                                        @if($salaryRecord->deductions > 0)
                                            <span class="text-rose-600 font-semibold">-Rs. {{ number_format($salaryRecord->deductions, 0) }}</span>
                                        @endif
                                        @if($salaryRecord->bonus == 0 && $salaryRecord->deductions == 0)
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-slate-400 text-xs">—</span>
                                @endif
                            </td>

                            <!-- Net Salary -->
                            <td class="px-6 py-4">
                                @if($salaryRecord)
                                    <div class="font-bold text-slate-900 text-base">Rs. {{ number_format($salaryRecord->net_salary, 0) }}</div>
                                @else
                                    <div class="font-bold text-slate-400 text-sm">Rs. {{ number_format($emp->salary ?? 0, 0) }}</div>
                                    <div class="text-[10px] text-slate-400">Estimated Base</div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="px-6 py-4">
                                @if($isPaid)
                                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Paid
                                    </span>
                                @elseif($salaryRecord && $salaryRecord->status === 'partial')
                                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Partial
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Unpaid
                                    </span>
                                @endif
                            </td>

                            <!-- Payment Details -->
                            <td class="px-6 py-4 text-xs">
                                @if($salaryRecord && $salaryRecord->payment_date)
                                    <div class="font-medium text-slate-700">{{ $salaryRecord->payment_date->format('M d, Y') }}</div>
                                    <div class="text-slate-400 capitalize font-medium">{{ str_replace('_', ' ', $salaryRecord->payment_method) }}</div>
                                @else
                                    <span class="text-slate-400 font-medium">Not processed</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($isPaid)
                                        <!-- View / Print Slip Button -->
                                        <button @click="openSlipModal({{ json_encode($salaryRecord) }}, {{ json_encode($emp) }})" 
                                                type="button" 
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg transition" 
                                                title="View & Print Slip">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                            Slip
                                        </button>

                                        <!-- Edit Payment Button -->
                                        <button @click="editSalary({{ json_encode($salaryRecord) }}, {{ json_encode($emp) }})" 
                                                type="button" 
                                                class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" 
                                                title="Edit Payment">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        <!-- Delete Payment Record -->
                                        <form action="{{ route('salaries.destroy', $salaryRecord) }}" method="POST" class="inline" onsubmit="return confirm('Revert and delete this salary payment record?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Payment Record">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Quick Pay Now Button -->
                                        <button @click="payForEmployee({{ json_encode($emp) }})" 
                                                type="button" 
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg shadow-sm shadow-emerald-600/20 transition cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pay Now
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-base font-medium text-slate-600">No staff salary records found</p>
                                <p class="text-xs text-slate-400 mt-1">Try changing filters or select a different salary month.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-slate-500 font-medium">
                Showing <span class="font-semibold text-slate-700">{{ $employees->firstItem() ?? 0 }}</span> to <span class="font-semibold text-slate-700">{{ $employees->lastItem() ?? 0 }}</span> of <span class="font-semibold text-slate-700">{{ $employees->total() }}</span> staff members
            </p>
            <div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PAY SALARY MODAL -->
    <!-- ========================================================================= -->
    <div x-show="isPayModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="isPayModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="isPayModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div x-show="isPayModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200">
                
                <form action="{{ route('salaries.store') }}" method="POST">
                    @csrf
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4 flex items-center justify-between text-white">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">Process Salary Disbursement</h3>
                                <p class="text-xs text-indigo-100">Disburse monthly compensation to employee</p>
                            </div>
                        </div>
                        <button type="button" @click="isPayModalOpen = false" class="text-white/70 hover:text-white transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Employee Select -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Select Staff Member <span class="text-rose-500">*</span></label>
                                <select name="employee_id" 
                                        x-model="formData.employee_id" 
                                        @change="onEmployeeSelect()" 
                                        required 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                                    <option value="">-- Choose Employee --</option>
                                    @foreach($allEmployees as $emp)
                                        <option value="{{ $emp->id }}" 
                                                data-salary="{{ $emp->salary }}" 
                                                data-department="{{ $emp->department->name ?? 'Unassigned' }}"
                                                data-designation="{{ $emp->designation->name ?? 'Staff' }}">
                                            {{ $emp->name }} ({{ $emp->designation->name ?? 'Staff' }} - Rs. {{ number_format($emp->salary ?? 0, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Salary Month -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Salary Month <span class="text-rose-500">*</span></label>
                                <input type="month" 
                                       name="salary_month" 
                                       x-model="formData.salary_month" 
                                       required 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Payment Date <span class="text-rose-500">*</span></label>
                                <input type="date" 
                                       name="payment_date" 
                                       x-model="formData.payment_date" 
                                       required 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                            </div>

                            <!-- Base Salary -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Basic Salary (Rs.) <span class="text-rose-500">*</span></label>
                                <input type="number" 
                                       step="0.01" 
                                       name="basic_salary" 
                                       x-model.number="formData.basic_salary" 
                                       @input="calculateNet()" 
                                       required 
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-bold text-slate-800">
                            </div>

                            <!-- Bonus / Allowance -->
                            <div>
                                <label class="block text-xs font-semibold text-emerald-700 uppercase tracking-wider mb-1.5">+ Bonus / Incentive (Rs.)</label>
                                <input type="number" 
                                       step="0.01" 
                                       name="bonus" 
                                       x-model.number="formData.bonus" 
                                       @input="calculateNet()" 
                                       placeholder="0.00" 
                                       class="w-full bg-emerald-50/40 border border-emerald-200 rounded-xl text-sm text-emerald-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 px-3.5 py-2.5 transition font-bold">
                            </div>

                            <!-- Deductions / Advance -->
                            <div>
                                <label class="block text-xs font-semibold text-rose-700 uppercase tracking-wider mb-1.5">- Deductions / Advance (Rs.)</label>
                                <input type="number" 
                                       step="0.01" 
                                       name="deductions" 
                                       x-model.number="formData.deductions" 
                                       @input="calculateNet()" 
                                       placeholder="0.00" 
                                       class="w-full bg-rose-50/40 border border-rose-200 rounded-xl text-sm text-rose-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 px-3.5 py-2.5 transition font-bold">
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Payment Method <span class="text-rose-500">*</span></label>
                                <select name="payment_method" 
                                        x-model="formData.payment_method" 
                                        required 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                                    <option value="cash">Cash</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="easypaisa">EasyPaisa / JazzCash</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>

                            <!-- Linked Payment Account (Optional) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Paid From Account (Optional)</label>
                                <select name="account_id" 
                                        x-model="formData.account_id" 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                                    <option value="">-- No Account Deduction --</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">
                                            {{ $acc->name }} (Bal: Rs. {{ number_format($acc->current_balance, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Status -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Payment Status <span class="text-rose-500">*</span></label>
                                <select name="status" 
                                        x-model="formData.status" 
                                        required 
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition font-medium">
                                    <option value="paid">Paid (Fully Disbursed)</option>
                                    <option value="partial">Partial</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>

                        <!-- Live Calculation Summary Card -->
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Total Net Payable Amount</span>
                                <p class="text-xs text-slate-500 mt-0.5">Basic (<span x-text="formatCurrency(formData.basic_salary)"></span>) + Bonus (<span x-text="formatCurrency(formData.bonus)"></span>) - Deductions (<span x-text="formatCurrency(formData.deductions)"></span>)</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black text-indigo-700">Rs. <span x-text="formatCurrency(formData.net_salary)"></span></span>
                                <input type="hidden" name="net_salary" :value="formData.net_salary">
                            </div>
                        </div>

                        <!-- Remarks / Notes -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Remarks / Reason (Optional)</label>
                            <input type="text" 
                                   name="notes" 
                                   x-model="formData.notes" 
                                   placeholder="e.g. Eid Bonus, Performance Incentive, Advance deduction..." 
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 px-3.5 py-2.5 transition">
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50 px-6 py-4 flex items-center justify-end gap-3 border-t border-slate-200/80">
                        <button type="button" 
                                @click="isPayModalOpen = false" 
                                class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl transition shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-600/25 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Confirm & Disburse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SALARY PAYSLIP / VOUCHER MODAL -->
    <!-- ========================================================================= -->
    <div x-show="isSlipModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Backdrop -->
            <div x-show="isSlipModalOpen" 
                 class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
                 @click="isSlipModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Slip Modal Panel -->
            <div x-show="isSlipModalOpen" 
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-200">
                
                <div class="p-6 sm:p-8" id="printablePayslip">
                    <!-- Slip Header -->
                    <div class="border-b-2 border-slate-800 pb-4 flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight uppercase">SALARY DISBURSEMENT SLIP</h2>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Tailor Boutique & Apparel Management</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                PAID VOUCHER
                            </span>
                            <div class="text-xs text-slate-400 mt-1 font-mono">VCH-#<span x-text="slipData.id || '0000'"></span></div>
                        </div>
                    </div>

                    <!-- Staff & Month Info -->
                    <div class="grid grid-cols-2 gap-4 py-4 border-b border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">Staff Name</span>
                            <span class="font-bold text-slate-800 text-sm" x-text="slipData.employee_name"></span>
                            <span class="text-slate-500 block" x-text="slipData.designation"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">Department</span>
                            <span class="font-bold text-slate-800" x-text="slipData.department"></span>
                            <span class="text-slate-500 block">Joining: <span x-text="slipData.joining_date || 'N/A'"></span></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">Salary Month</span>
                            <span class="font-bold text-indigo-700 text-sm" x-text="slipData.salary_month_formatted"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold uppercase tracking-wider block text-[10px]">Payment Date</span>
                            <span class="font-bold text-slate-800" x-text="slipData.payment_date_formatted"></span>
                            <span class="text-slate-500 block uppercase" x-text="slipData.payment_method"></span>
                        </div>
                    </div>

                    <!-- Breakdown Table -->
                    <div class="py-4 space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-600 font-medium">Basic Salary</span>
                            <span class="font-bold text-slate-800">Rs. <span x-text="formatCurrency(slipData.basic_salary)"></span></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 text-emerald-600">
                            <span class="font-medium">+ Bonus / Incentive</span>
                            <span class="font-bold">Rs. <span x-text="formatCurrency(slipData.bonus)"></span></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 text-rose-600">
                            <span class="font-medium">- Deductions / Advances</span>
                            <span class="font-bold">Rs. <span x-text="formatCurrency(slipData.deductions)"></span></span>
                        </div>
                        <div class="flex justify-between py-3 border-t-2 border-slate-800 text-base font-black text-slate-900 bg-slate-50 px-3 rounded-xl mt-2">
                            <span>Net Salary Paid</span>
                            <span class="text-indigo-700">Rs. <span x-text="formatCurrency(slipData.net_salary)"></span></span>
                        </div>
                    </div>

                    <!-- Remarks if any -->
                    <template x-if="slipData.notes">
                        <div class="bg-slate-50 p-3 rounded-xl text-xs text-slate-600 mb-4 border border-slate-200/60">
                            <span class="font-bold text-slate-700">Notes:</span> <span x-text="slipData.notes"></span>
                        </div>
                    </template>

                    <!-- Signature Box -->
                    <div class="grid grid-cols-2 gap-8 pt-8 mt-4 border-t border-dashed border-slate-300 text-center text-xs">
                        <div>
                            <div class="border-b border-slate-400 w-32 mx-auto mb-1"></div>
                            <span class="text-slate-500 font-medium">Employee Signature</span>
                        </div>
                        <div>
                            <div class="border-b border-slate-400 w-32 mx-auto mb-1"></div>
                            <span class="text-slate-500 font-medium">Authorized Signatory</span>
                        </div>
                    </div>
                </div>

                <!-- Slip Action Footer -->
                <div class="bg-slate-50 px-6 py-4 flex items-center justify-between border-t border-slate-200">
                    <button type="button" @click="isSlipModalOpen = false" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                        Close
                    </button>
                    <button type="button" 
                            @click="printSlip()" 
                            class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-xl shadow-lg transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Payslip
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function salaryManager() {
    return {
        isPayModalOpen: false,
        isSlipModalOpen: false,
        formData: {
            employee_id: '',
            salary_month: '{{ $month }}',
            payment_date: '{{ \Carbon\Carbon::today()->format('Y-m-d') }}',
            basic_salary: 0,
            bonus: 0,
            deductions: 0,
            net_salary: 0,
            payment_method: 'cash',
            account_id: '',
            status: 'paid',
            notes: ''
        },
        slipData: {},

        openPayModal() {
            this.formData = {
                employee_id: '',
                salary_month: '{{ $month }}',
                payment_date: '{{ \Carbon\Carbon::today()->format('Y-m-d') }}',
                basic_salary: 0,
                bonus: 0,
                deductions: 0,
                net_salary: 0,
                payment_method: 'cash',
                account_id: '',
                status: 'paid',
                notes: ''
            };
            this.isPayModalOpen = true;
        },

        payForEmployee(emp) {
            this.formData.employee_id = emp.id;
            this.formData.salary_month = '{{ $month }}';
            this.formData.payment_date = '{{ \Carbon\Carbon::today()->format('Y-m-d') }}';
            this.formData.basic_salary = parseFloat(emp.salary) || 0;
            this.formData.bonus = 0;
            this.formData.deductions = 0;
            this.formData.payment_method = 'cash';
            this.formData.account_id = '';
            this.formData.status = 'paid';
            this.formData.notes = '';
            this.calculateNet();
            this.isPayModalOpen = true;
        },

        editSalary(salaryRecord, emp) {
            this.formData.employee_id = emp.id;
            this.formData.salary_month = salaryRecord.salary_month;
            this.formData.payment_date = (salaryRecord.payment_date || '').substring(0, 10);
            this.formData.basic_salary = parseFloat(salaryRecord.basic_salary) || 0;
            this.formData.bonus = parseFloat(salaryRecord.bonus) || 0;
            this.formData.deductions = parseFloat(salaryRecord.deductions) || 0;
            this.formData.net_salary = parseFloat(salaryRecord.net_salary) || 0;
            this.formData.payment_method = salaryRecord.payment_method || 'cash';
            this.formData.account_id = salaryRecord.account_id || '';
            this.formData.status = salaryRecord.status || 'paid';
            this.formData.notes = salaryRecord.notes || '';
            this.isPayModalOpen = true;
        },

        onEmployeeSelect() {
            const selectEl = document.querySelector('select[name="employee_id"]');
            const selectedOpt = selectEl.options[selectEl.selectedIndex];
            if (selectedOpt && selectedOpt.value) {
                const sal = selectedOpt.getAttribute('data-salary');
                this.formData.basic_salary = parseFloat(sal) || 0;
                this.calculateNet();
            }
        },

        calculateNet() {
            const basic = parseFloat(this.formData.basic_salary) || 0;
            const bonus = parseFloat(this.formData.bonus) || 0;
            const deductions = parseFloat(this.formData.deductions) || 0;
            this.formData.net_salary = Math.max(0, basic + bonus - deductions);
        },

        formatCurrency(num) {
            const n = parseFloat(num) || 0;
            return n.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },

        openSlipModal(salaryRecord, emp) {
            const dateObj = new Date(salaryRecord.payment_date || new Date());
            const monthStr = salaryRecord.salary_month || '{{ $month }}';
            
            this.slipData = {
                id: salaryRecord.id,
                employee_name: emp.name,
                designation: emp.designation ? emp.designation.name : 'Staff Member',
                department: emp.department ? emp.department.name : 'Unassigned',
                joining_date: emp.joining_date,
                salary_month_formatted: monthStr,
                payment_date_formatted: dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                payment_method: (salaryRecord.payment_method || 'Cash').replace('_', ' '),
                basic_salary: salaryRecord.basic_salary,
                bonus: salaryRecord.bonus,
                deductions: salaryRecord.deductions,
                net_salary: salaryRecord.net_salary,
                notes: salaryRecord.notes
            };
            this.isSlipModalOpen = true;
        },

        printSlip() {
            const printContents = document.getElementById('printablePayslip').innerHTML;
            const originalContents = document.body.innerHTML;

            const printWindow = window.open('', '', 'height=650,width=800');
            printWindow.document.write('<html><head><title>Salary Payslip</title>');
            printWindow.document.write('<script src="https://cdn.tailwindcss.com"><\/script>');
            printWindow.document.write('</head><body class="p-8 bg-white">');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }
    }
}
</script>
@endsection
