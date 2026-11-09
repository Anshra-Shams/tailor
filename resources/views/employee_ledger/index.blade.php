@extends('layouts.app')

@section('title', 'Employee Ledger')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Employee Ledger</h2>
            <p class="text-sm text-slate-500 mt-1">Track employee salary payouts, advances, deductions, production orders, and attendance</p>
        </div>

        <!-- Quick Employee Selector -->
        <div class="flex items-center gap-3">
            <div class="relative min-w-[260px]">
                <select onchange="if(this.value) window.location.href='/employee-ledger/' + this.value" 
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 shadow-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">Select Employee to View Ledger...</option>
                    @foreach ($allEmployees as $emp)
                        <option value="{{ $emp->id }}">
                            {{ $emp->name }} — {{ $emp->department?->name ?? 'Staff' }} ({{ ucfirst($emp->salary_type) }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Stats Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Staff -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Employees</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['total_employees'] }}</h3>
                <p class="text-xs text-emerald-600 mt-0.5 font-medium">{{ $stats['active_employees'] }} active</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Disbursed -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Disbursed (Paid)</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($stats['total_paid'], 0) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">All salary payouts</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Total Bonus -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Bonuses</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">Rs. {{ number_format($stats['total_bonus'], 0) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Performance & incentives</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>

        <!-- Total Deductions -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Deductions / Advances</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1">Rs. {{ number_format($stats['total_deductions'], 0) }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">Advances & deductions</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Employee Directory Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800">Staff Ledger Directory</h3>
                <p class="text-xs text-slate-500 mt-0.5">Select any employee to view their detailed account statement, payouts, and production history</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/75 text-[11px] font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Employee</th>
                        <th class="px-6 py-4">Department / Designation</th>
                        <th class="px-6 py-4">Salary Structure</th>
                        <th class="px-6 py-4 text-right">Disbursed (Paid)</th>
                        <th class="px-6 py-4 text-right">Deductions</th>
                        <th class="px-6 py-4 text-center">Payments</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($employees as $emp)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center text-sm shadow-xs shrink-0">
                                        {{ strtoupper(substr($emp->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('employee-ledger.show', $emp) }}" class="font-bold text-slate-800 hover:text-indigo-600 transition">
                                            {{ $emp->name }}
                                        </a>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $emp->phone ?? 'No phone' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $emp->department?->name ?? '—' }}
                                </span>
                                <p class="text-xs text-slate-400 mt-1">{{ $emp->designation?->name ?? 'General Staff' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if ($emp->salary_type === 'monthly')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Monthly
                                    </span>
                                @elseif ($emp->salary_type === 'weekly')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                        Weekly
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Project / Piece
                                    </span>
                                @endif
                                <p class="text-xs font-semibold text-slate-700 mt-1">Rs. {{ number_format((float) $emp->salary, 2) }}</p>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600 whitespace-nowrap">
                                Rs. {{ number_format((float) ($emp->total_disbursed ?? 0), 2) }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-rose-600 whitespace-nowrap">
                                Rs. {{ number_format((float) ($emp->total_deductions ?? 0), 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ $emp->total_payments_count }} records
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('employee-ledger.show', $emp) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white font-semibold text-xs transition duration-150">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>View Ledger</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                No employees found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($employees->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
