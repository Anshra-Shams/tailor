@extends('layouts.app')

@section('title', isset($employee) && $employee ? $employee->name . ' — Employee Ledger' : 'Employee Ledger')

@section('content')
<div class="space-y-4" x-data="employeeLedger()">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Employee Ledger</h2>
            <p class="text-sm text-slate-500 mt-0.5">View detailed account statement, work history, and payment records for staff members</p>
        </div>
        @if (isset($employee) && $employee)
            <div class="flex items-center gap-3">
                <a href="{{ route('employee-ledger.print', ['employee' => $employee->id, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold px-4 py-2.5 rounded-xl shadow-xs transition text-sm">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Print Ledger</span>
                </a>
                <button @click="openPayModal()"
                        type="button"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-indigo-500/20 transition text-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>Pay Salary / Advance</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filter Card (Select Staff, From Date, To Date, Load, Reset) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-xs">
        <form method="GET" action="{{ route('employee-ledger.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            <div class="lg:col-span-2 sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Select Staff</label>
                <select name="employee_id" required 
                        class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium transition">
                    <option value="">-- Select Staff to View Ledger --</option>
                    @foreach ($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ isset($employee) && $employee && $employee->id == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->department?->name ?? 'Staff' }} - {{ ucfirst($emp->salary_type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">From Date</label>
                <input type="date" name="from_date" value="{{ $fromDate ?? now()->startOfMonth()->format('Y-m-d') }}"
                       class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">To Date</label>
                <input type="date" name="to_date" value="{{ $toDate ?? now()->format('Y-m-d') }}"
                       class="w-full bg-slate-50/70 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 font-medium transition">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" 
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md shadow-indigo-500/20 transition text-sm cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span>Load</span>
                </button>
                <a href="{{ route('employee-ledger.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-xl transition text-sm"
                   title="Reset Filter">
                    <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    @if (!isset($employee) || !$employee)
        <!-- Empty State (Matching Image 2) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-20 text-center shadow-xs">
            <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-500">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0021.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 003.065 7.097A9.716 9.716 0 0012 21.75a9.716 9.716 0 006.685-2.653zm-12.54-1.285A7.486 7.486 0 0112 15a7.486 7.486 0 015.855 2.812A8.224 8.224 0 0112 20.25a8.224 8.224 0 01-5.855-2.438zM15.75 9a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-base font-semibold text-slate-700">Select a staff member to view their ledger</h3>
        </div>
    @else
        <!-- Employee Profile Banner (Compact) -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl px-5 py-3 text-white shadow-lg shadow-indigo-500/15 relative overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-white/20 backdrop-blur-md text-white font-bold flex items-center justify-center text-base shadow-xs shrink-0 border border-white/30">
                        {{ strtoupper(substr($employee->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold tracking-tight text-white leading-tight">{{ $employee->name }}</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-white/20 text-white">
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-xs text-indigo-100">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $employee->phone ?: 'No phone' }}
                            </span>
                            @if ($employee->email)
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $employee->email }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Joined: {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('M d, Y') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Salary Structure Pill Box (Compact) -->
                <div class="flex items-center gap-3.5 bg-white/15 backdrop-blur-md rounded-xl px-3.5 py-1.5 border border-white/20">
                    <div>
                        <p class="text-[9px] uppercase font-bold tracking-wider text-indigo-100">Rate / Salary</p>
                        <p class="text-base font-black text-white leading-tight">Rs. {{ number_format((float) $employee->salary, 2) }}</p>
                        <p class="text-[10px] text-indigo-200 font-medium capitalize">{{ $employee->salary_type }} Basis</p>
                    </div>
                    <div class="h-8 w-[1px] bg-white/20"></div>
                    <div>
                        <p class="text-[9px] uppercase font-bold tracking-wider text-indigo-100">Department</p>
                        <p class="text-xs font-bold text-white leading-tight">{{ $employee->department?->name ?? 'General' }}</p>
                        <p class="text-[10px] text-indigo-200 font-medium">{{ $employee->designation?->name ?? 'Staff' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary KPI Cards Row (7 Cards) -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
            <!-- Due Balance (Net Outstanding) -->
            <div class="bg-white rounded-2xl p-4 border {{ $stats['due_balance'] < 0 ? 'border-rose-200 bg-rose-50/20' : ($stats['due_balance'] > 0 ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200/80') }} shadow-xs">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Due Balance</p>
                    @if ($stats['due_balance'] < 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-rose-100 text-rose-700">Advance</span>
                    @elseif ($stats['due_balance'] > 0)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-700">Payable</span>
                    @else
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600">Settled</span>
                    @endif
                </div>
                <h4 class="text-xl font-black {{ $stats['due_balance'] < 0 ? 'text-rose-600' : ($stats['due_balance'] > 0 ? 'text-emerald-600' : 'text-slate-800') }} mt-1">
                    {{ $stats['due_balance'] < 0 ? '- Rs. ' . number_format(abs($stats['due_balance']), 0) : 'Rs. ' . number_format($stats['due_balance'], 0) }}
                </h4>
                <p class="text-[11px] {{ $stats['due_balance'] < 0 ? 'text-rose-600' : ($stats['due_balance'] > 0 ? 'text-emerald-600' : 'text-slate-400') }} font-medium mt-0.5 truncate">
                    @if ($stats['due_balance'] < 0)
                        Worker par advance
                    @elseif ($stats['due_balance'] > 0)
                        Worker ko dena baaqi
                    @else
                        Hisaab clear hai
                    @endif
                </p>
            </div>

            <!-- Disbursed (Paid) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Disbursed</p>
                <h4 class="text-xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($stats['total_disbursed'], 0) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Net amount paid</p>
            </div>

            <!-- Work / Basic Earned -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                    {{ $employee->salary_type === 'project' ? 'Work Earned' : 'Basic Earned' }}
                </p>
                <h4 class="text-xl font-bold text-slate-800 mt-1">Rs. {{ number_format($stats['total_work_earned'], 0) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                    {{ $employee->salary_type === 'project' ? 'Assigned work gross' : 'Salary gross' }}
                </p>
            </div>

            <!-- Bonuses -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Total Bonuses</p>
                <h4 class="text-xl font-bold text-indigo-600 mt-1">Rs. {{ number_format($stats['total_bonus'], 0) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Incentives awarded</p>
            </div>

            <!-- Deductions / Advance -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Deductions / Adv</p>
                <h4 class="text-xl font-bold text-rose-600 mt-1">Rs. {{ number_format($stats['total_deductions'], 0) }}</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Advances & cuts</p>
            </div>

            <!-- Assigned Orders: 1 / Rs. 500 -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Assigned Orders</p>
                <h4 class="text-xl font-bold text-purple-600 mt-1">
                    {{ $stats['total_orders'] }} <span class="text-slate-400 text-sm font-normal">/</span> Rs. {{ number_format($stats['assigned_work_amount'] ?? 0, 0) }}
                </h4>
                <p class="text-[11px] text-emerald-600 font-medium mt-0.5">{{ $stats['completed_orders'] }} completed</p>
            </div>

            <!-- Attendance -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Attendance</p>
                <h4 class="text-xl font-bold text-slate-800 mt-1">{{ $stats['present_days'] }} <span class="text-xs font-normal text-slate-400">days</span></h4>
                <p class="text-[11px] text-rose-500 font-medium mt-0.5">{{ $stats['absent_days'] }} absents</p>
            </div>
        </div>

        <!-- Ledger Tabs Container -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            
            <!-- Tab Headers -->
            <div class="border-b border-slate-200 flex flex-wrap items-center px-4 pt-3 gap-2 bg-slate-50/50">
                <button @click="tab = 'all'"
                        type="button"
                        :class="tab === 'all' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
                        class="px-4 py-2.5 text-xs font-semibold rounded-t-xl border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Consolidated Ledger ({{ $ledgerEntries->count() }})</span>
                </button>

                <button @click="tab = 'salaries'"
                        type="button"
                        :class="tab === 'salaries' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
                        class="px-4 py-2.5 text-xs font-semibold rounded-t-xl border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Salary Payments ({{ $salaries->count() }})</span>
                </button>

                <button @click="tab = 'orders'"
                        type="button"
                        :class="tab === 'orders' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
                        class="px-4 py-2.5 text-xs font-semibold rounded-t-xl border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Production & Orders ({{ $orders->count() }})</span>
                </button>

                <button @click="tab = 'attendance'"
                        type="button"
                        :class="tab === 'attendance' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'"
                        class="px-4 py-2.5 text-xs font-semibold rounded-t-xl border-b-2 transition flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Attendance Log ({{ $attendances->count() }})</span>
                </button>
            </div>

            <!-- TAB 1: CONSOLIDATED LEDGER -->
            <div x-show="tab === 'all'" class="divide-y divide-slate-100">
                @if ($ledgerEntries->isEmpty())
                    <div class="px-6 py-16 text-center text-slate-400">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="font-medium text-slate-600">No ledger transactions found</p>
                        <p class="text-xs text-slate-400 mt-1">There are no recorded salary payouts or assigned orders for this time range.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3.5">Date</th>
                                    <th class="px-5 py-3.5">Ref #</th>
                                    <th class="px-5 py-3.5">Type & Description</th>
                                    <th class="px-5 py-3.5 text-right">Work Earned (+)</th>
                                    <th class="px-5 py-3.5 text-right">Bonus (+)</th>
                                    <th class="px-5 py-3.5 text-right">Deduction (-)</th>
                                    <th class="px-5 py-3.5 text-right">Disbursed (Debit)</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($ledgerEntries as $entry)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <p class="font-bold text-slate-800 text-xs">{{ $entry['date']->format('d M Y') }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $entry['date']->format('h:i A') }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-indigo-600 font-semibold">
                                            {{ $entry['ref_id'] }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-2">
                                                @if ($entry['type'] === 'salary')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        Salary
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        Order Work
                                                    </span>
                                                @endif
                                                <span class="font-semibold text-slate-800 text-xs">{{ $entry['title'] }}</span>
                                            </div>
                                            <p class="text-xs text-slate-400 mt-0.5">{{ $entry['description'] }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold text-indigo-600 whitespace-nowrap text-xs">
                                            {{ $entry['credit'] > 0 ? '+ Rs. ' . number_format($entry['credit'], 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-medium text-indigo-600 whitespace-nowrap text-xs">
                                            {{ $entry['bonus'] > 0 ? '+ Rs. ' . number_format($entry['bonus'], 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-medium text-rose-600 whitespace-nowrap text-xs">
                                            {{ $entry['deduction'] > 0 ? '- Rs. ' . number_format($entry['deduction'], 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-bold text-emerald-600 whitespace-nowrap text-xs">
                                            {{ $entry['debit'] > 0 ? 'Rs. ' . number_format($entry['debit'], 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ in_array($entry['status'], ['paid', 'completed', 'delivered']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $entry['status'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- TAB 2: SALARY PAYMENTS -->
            <div x-show="tab === 'salaries'" class="divide-y divide-slate-100">
                @if ($salaries->isEmpty())
                    <div class="px-6 py-16 text-center text-slate-400">
                        <p class="font-medium text-slate-600">No salary payment records found</p>
                        <p class="text-xs text-slate-400 mt-1">Click "Pay Salary / Advance" above to record a new payment.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3.5">Payment Date</th>
                                    <th class="px-5 py-3.5">Salary Period</th>
                                    <th class="px-5 py-3.5 text-right">Basic</th>
                                    <th class="px-5 py-3.5 text-right">Bonus</th>
                                    <th class="px-5 py-3.5 text-right">Deductions</th>
                                    <th class="px-5 py-3.5 text-right">Net Paid</th>
                                    <th class="px-5 py-3.5">Method & Account</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($salaries as $sal)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5 whitespace-nowrap font-medium text-slate-800 text-xs">
                                            {{ $sal->payment_date ? \Carbon\Carbon::parse($sal->payment_date)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                            <span class="font-semibold text-slate-700">
                                                {{ $sal->from_date && $sal->to_date ? \Carbon\Carbon::parse($sal->from_date)->format('d M') . ' - ' . \Carbon\Carbon::parse($sal->to_date)->format('d M Y') : $sal->salary_month }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-xs font-semibold text-slate-700 whitespace-nowrap">
                                            Rs. {{ number_format((float) $sal->basic_salary, 2) }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-xs font-semibold text-indigo-600 whitespace-nowrap">
                                            {{ (float) $sal->bonus > 0 ? '+ Rs. ' . number_format((float) $sal->bonus, 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-xs font-semibold text-rose-600 whitespace-nowrap">
                                            {{ (float) $sal->deductions > 0 ? '- Rs. ' . number_format((float) $sal->deductions, 2) : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right text-xs font-extrabold text-emerald-600 whitespace-nowrap">
                                            Rs. {{ number_format((float) $sal->net_salary, 2) }}
                                        </td>
                                        <td class="px-5 py-3.5 text-xs whitespace-nowrap">
                                            <p class="font-semibold text-slate-700 capitalize">{{ $sal->payment_method ?? 'cash' }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $sal->account?->name ?? 'Default Cash Drawer' }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $sal->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $sal->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-slate-500 max-w-xs truncate">
                                            {{ $sal->notes ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- TAB 3: ASSIGNED ORDERS & PRODUCTION -->
            <div x-show="tab === 'orders'" class="divide-y divide-slate-100">
                @if ($orders->isEmpty())
                    <div class="px-6 py-16 text-center text-slate-400">
                        <p class="font-medium text-slate-600">No production orders assigned</p>
                        <p class="text-xs text-slate-400 mt-1">This employee has not been assigned cutting or stitching for any orders yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3.5">Order #</th>
                                    <th class="px-5 py-3.5">Role</th>
                                    <th class="px-5 py-3.5">Customer & Service</th>
                                    <th class="px-5 py-3.5 text-center">Qty</th>
                                    <th class="px-5 py-3.5">Order Date</th>
                                    <th class="px-5 py-3.5">Due / Completed</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-right">Order Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($orders as $ord)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <a href="{{ route('orders.index', ['q' => $ord->id]) }}" class="font-bold text-indigo-600 hover:underline">
                                                #{{ str_pad($ord->id, 4, '0', STR_PAD_LEFT) }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs">
                                            @if ($ord->cutting_employee_id == $employee->id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Cutting Master
                                                </span>
                                            @endif
                                            @if ($ord->stitching_employee_id == $employee->id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    Stitching Master
                                                </span>
                                            @endif
                                            @if ($ord->assigned_employee_id == $employee->id && $ord->cutting_employee_id != $employee->id && $ord->stitching_employee_id != $employee->id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    Assigned
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 text-xs">
                                            <p class="font-bold text-slate-800">{{ $ord->customer?->name ?? 'Walk-in' }}</p>
                                            <p class="text-slate-400 mt-0.5">{{ $ord->service?->name ?? 'Tailoring Service' }}</p>
                                        </td>
                                        <td class="px-5 py-3.5 text-center font-bold text-xs">
                                            {{ $ord->quantity }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                            {{ $ord->order_date ? \Carbon\Carbon::parse($ord->order_date)->format('d M Y') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                            {{ $ord->completed_date ? \Carbon\Carbon::parse($ord->completed_date)->format('d M Y') : ($ord->due_date ? 'Due: ' . \Carbon\Carbon::parse($ord->due_date)->format('d M Y') : '—') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $ord->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $ord->status }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-semibold text-slate-800 whitespace-nowrap text-xs">
                                            Rs. {{ number_format((float) ($ord->price * $ord->quantity), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- TAB 4: ATTENDANCE LOG -->
            <div x-show="tab === 'attendance'" class="divide-y divide-slate-100">
                @if ($attendances->isEmpty())
                    <div class="px-6 py-16 text-center text-slate-400">
                        <p class="font-medium text-slate-600">No attendance logs found</p>
                        <p class="text-xs text-slate-400 mt-1">No attendance records have been marked for this employee in the selected range.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                                <tr>
                                    <th class="px-5 py-3.5">Date</th>
                                    <th class="px-5 py-3.5">Day</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5">Check In</th>
                                    <th class="px-5 py-3.5">Check Out</th>
                                    <th class="px-5 py-3.5">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($attendances as $att)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800 text-xs">
                                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                            {{ \Carbon\Carbon::parse($att->date)->format('l') }}
                                        </td>
                                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                            @if ($att->status === 'present')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Present
                                                </span>
                                            @elseif ($att->status === 'absent')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                                    Absent
                                                </span>
                                            @elseif ($att->status === 'late')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                                    Late
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $att->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                            {{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                            {{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : '—' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-xs text-slate-400">
                                            {{ $att->notes ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>

        <!-- MODAL: PAY SALARY / ADVANCE -->
        <div x-show="showPayModal" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="showPayModal = false"
                 class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Pay Salary / Advance</h3>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $employee->name }} &middot; Rate: Rs. {{ number_format((float) $employee->salary, 2) }}</p>
                    </div>
                    <button @click="showPayModal = false" type="button" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('salaries.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Period From <span class="text-rose-500">*</span></label>
                            <input type="date" name="from_date" required x-model="payForm.from_date"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Period To <span class="text-rose-500">*</span></label>
                            <input type="date" name="to_date" required x-model="payForm.to_date"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="payment_date" required x-model="payForm.payment_date"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Basic Salary <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" name="basic_salary" required x-model.number="payForm.basic_salary" @input="recalcNet()"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Bonus (+)</label>
                            <input type="number" step="0.01" name="bonus" x-model.number="payForm.bonus" @input="recalcNet()"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-indigo-600 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Deductions / Adv (-)</label>
                            <input type="number" step="0.01" name="deductions" x-model.number="payForm.deductions" @input="recalcNet()"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-rose-600 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="bg-indigo-50/70 border border-indigo-100 rounded-xl p-3 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider">Net Amount To Pay</p>
                            <p class="text-xs text-slate-500">(Basic + Bonus - Deductions)</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-black text-indigo-700">Rs. <span x-text="formatMoney(payForm.net_salary)"></span></span>
                            <input type="hidden" name="net_salary" :value="payForm.net_salary">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Debit From Account</label>
                            <select name="account_id"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="">Cash Drawer (Default)</option>
                                @foreach ($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->name }} (Rs. {{ number_format((float) $acc->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                            <select name="status" required
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                                <option value="paid" selected>Paid</option>
                                <option value="pending">Pending</option>
                                <option value="partial">Partial</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Notes / Description <span class="text-slate-400 font-normal text-[11px]">(optional)</span></label>
                        <textarea name="notes" rows="2" placeholder="e.g. Monthly salary disbursed or advance payment..."
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" @click="showPayModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl shadow-md shadow-indigo-500/20 transition">
                            Confirm Payout
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>

<script>
    function employeeLedger() {
        return {
            tab: '{{ $activeTab ?? "all" }}',
            showPayModal: false,
            payForm: {
                from_date: '{{ $fromDate ?? now()->startOfMonth()->format("Y-m-d") }}',
                to_date: '{{ $toDate ?? now()->format("Y-m-d") }}',
                payment_date: '{{ now()->format("Y-m-d") }}',
                basic_salary: {{ isset($employee) && $employee ? (float) ($employee->salary_type === 'project' ? ($stats['balance_payable'] > 0 ? $stats['balance_payable'] : ($stats['total_work_earned'] > 0 ? $stats['total_work_earned'] : $employee->salary)) : ($employee->salary ?? 0)) : 0 }},
                bonus: 0,
                deductions: 0,
                net_salary: {{ isset($employee) && $employee ? (float) ($employee->salary_type === 'project' ? ($stats['balance_payable'] > 0 ? $stats['balance_payable'] : ($stats['total_work_earned'] > 0 ? $stats['total_work_earned'] : $employee->salary)) : ($employee->salary ?? 0)) : 0 }},
            },
            openPayModal() {
                this.recalcNet();
                this.showPayModal = true;
            },
            recalcNet() {
                const basic = parseFloat(this.payForm.basic_salary) || 0;
                const bonus = parseFloat(this.payForm.bonus) || 0;
                const ded = parseFloat(this.payForm.deductions) || 0;
                this.payForm.net_salary = Math.max(0, basic + bonus - ded);
            },
            formatMoney(val) {
                return (parseFloat(val) || 0).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        };
    }
</script>
@endsection
