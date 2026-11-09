@extends('layouts.app')

@section('title', 'Reports & Financial Summary')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'orders' }">

    {{-- Top Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <div class="p-2 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <span>Business & Financial Report</span>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Date-wise</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Summary of orders, billing amounts, receipts, salaries, expenses, and pending balances.
            </p>
        </div>

        {{-- Header Actions --}}
        <div class="flex items-center gap-2.5">
            <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition-all hover:border-slate-400">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.056-.367-2.155-.367-3.279 0-4.041 3.266-7.32 7.297-7.32s7.297 3.279 7.297 7.32c0 1.124-.128 2.223-.367 3.279M3 13.829A10.957 10.957 0 0113.65 3c4.636 0 8.618 2.87 10.23 6.945M6.75 19.5h10.5M6.75 16.5h10.5m-12 6h13.5a1.5 1.5 0 001.5-1.5V15a1.5 1.5 0 00-1.5-1.5H4.5A1.5 1.5 0 003 15v6a1.5 1.5 0 001.5 1.5z" />
                </svg>
                <span>Print Report</span>
            </button>
            <a href="{{ route('reports.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Reset</span>
            </a>
        </div>
    </div>

    {{-- Printable Document Header (Only shows on print) --}}
    <div class="hidden print:block pb-4 mb-4 border-b border-slate-300">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Prowave Tailors - Financial & Business Report</h1>
                <p class="text-xs text-slate-600 mt-1">Generated on {{ now()->format('d M, Y h:i A') }}</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold text-slate-800">Date Range:</span>
                <span class="text-sm text-slate-700 font-medium">{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Date Filter Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm print:hidden">
        <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
            {{-- Quick Preset Badges --}}
            <div class="flex flex-wrap items-center gap-2 pb-3 border-b border-slate-100">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mr-1">Quick Select:</span>
                <a href="{{ route('reports.index', ['preset' => 'today']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'today' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Today
                </a>
                <a href="{{ route('reports.index', ['preset' => 'yesterday']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'yesterday' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Yesterday
                </a>
                <a href="{{ route('reports.index', ['preset' => 'this_week']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'this_week' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    This Week
                </a>
                <a href="{{ route('reports.index', ['preset' => 'this_month']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ ($preset === 'this_month' || (empty($preset) && $fromDate === now()->startOfMonth()->toDateString() && $toDate === now()->toDateString())) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    This Month
                </a>
                <a href="{{ route('reports.index', ['preset' => 'last_month']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'last_month' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Last Month
                </a>
                <a href="{{ route('reports.index', ['preset' => 'this_year']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'this_year' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    This Year
                </a>
                <a href="{{ route('reports.index', ['preset' => 'all']) }}"
                   class="px-3 py-1 text-xs font-medium rounded-lg transition-colors {{ $preset === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Time
                </a>
            </div>

            {{-- Custom Date Form --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        From Date
                    </label>
                    <div class="relative">
                        <input type="date" name="from_date" value="{{ $fromDate }}"
                            class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all font-medium text-slate-700"
                            required>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">
                        To Date
                    </label>
                    <div class="relative">
                        <input type="date" name="to_date" value="{{ $toDate }}"
                            class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 transition-all font-medium text-slate-700"
                            required>
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Filter Data</span>
                    </button>
                </div>

                <div class="text-xs text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200 text-center flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Showing <strong>{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</strong></span>
                </div>
            </div>
        </form>
    </div>

    {{-- ================= ROW 1: CORE STATS CARDS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- 1. Total Orders Card (order kitny ay) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Orders</span>
                    <h3 class="text-3xl font-extrabold text-slate-800 mt-1">{{ number_format($totalOrdersCount) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Orders placed in selected period</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span title="Completed/Delivered"><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-1"></span>{{ $ordersStatusCounts['completed'] + $ordersStatusCounts['delivered'] }} Done</span>
                <span title="In Progress"><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1"></span>{{ $ordersStatusCounts['in_progress'] }} In Progress</span>
                <span title="Pending"><span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-1"></span>{{ $ordersStatusCounts['pending'] }} Pending</span>
            </div>
        </div>

        {{-- 2. Total Order Amount (kitny amunt bany) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Order Amount</span>
                    <h3 class="text-3xl font-extrabold text-indigo-600 mt-1">Rs. {{ number_format($totalOrderAmount) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Total billing value of orders</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center group-hover:bg-purple-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Avg. / Order:</span>
                <span class="font-bold text-slate-700">Rs. {{ $totalOrdersCount > 0 ? number_format($totalOrderAmount / $totalOrdersCount) : 0 }}</span>
            </div>
        </div>

        {{-- 3. Received Amount (kitny rescipt kiya kitna amount) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Received (Receipts)</span>
                    <h3 class="text-3xl font-extrabold text-emerald-600 mt-1">Rs. {{ number_format($totalReceipts) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ number_format($receiptsCount) }} payment receipts collected</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Collection rate:</span>
                <span class="font-bold text-emerald-600">
                    {{ $totalOrderAmount > 0 ? round(($totalReceipts / $totalOrderAmount) * 100, 1) : 0 }}%
                </span>
            </div>
        </div>

        {{-- 4. Remaining Balance (kitny amount rahy gaya ha baki) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Remaining Balance (Due)</span>
                    <h3 class="text-3xl font-extrabold text-amber-600 mt-1">Rs. {{ number_format($periodOrdersDue) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Pending receivable from period orders</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center group-hover:bg-amber-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Status:</span>
                <span class="font-bold text-amber-700">
                    {{ $periodOrdersDue > 0 ? 'Pending Collection' : 'All Cleared' }}
                </span>
            </div>
        </div>

    </div>

    {{-- ================= ROW 2: OUTFLOWS & NET BALANCE CARDS ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- 5. Salaries Paid (salry kinty pay ki ha) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Salaries Paid</span>
                    <h3 class="text-3xl font-extrabold text-rose-600 mt-1">Rs. {{ number_format($totalSalariesPaid) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ number_format($salariesCount) }} salary disbursements</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center group-hover:bg-rose-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Payroll outflow:</span>
                <span class="font-bold text-rose-600">Disbursed</span>
            </div>
        </div>

        {{-- 6. Total Expenses (kitna expanse ay ha) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Expenses</span>
                    <h3 class="text-3xl font-extrabold text-red-600 mt-1">Rs. {{ number_format($totalExpenses) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ number_format($expensesCount) }} expense vouchers</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center group-hover:bg-red-100 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Shop overheads:</span>
                <span class="font-bold text-red-600">Spent</span>
            </div>
        </div>

        {{-- 7. Total Outflow (Expenses + Salaries) --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Outflow</span>
                    <h3 class="text-3xl font-extrabold text-slate-700 mt-1">Rs. {{ number_format($totalOutflow) }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Salaries + Expenses combined</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-slate-200 group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                <span>Salaries: Rs. {{ number_format($totalSalariesPaid) }}</span>
                <span>Expenses: Rs. {{ number_format($totalExpenses) }}</span>
            </div>
        </div>

        {{-- 8. Net Balance (Receipts minus Outflow) --}}
        <div class="rounded-2xl p-5 border shadow-sm transition-all group {{ $netCashFlow >= 0 ? 'bg-gradient-to-br from-emerald-600 to-teal-700 text-white border-emerald-600 shadow-emerald-600/20' : 'bg-gradient-to-br from-rose-600 to-red-700 text-white border-rose-600 shadow-rose-600/20' }}">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-100 opacity-90">Net Balance</span>
                    <h3 class="text-3xl font-extrabold text-white mt-1">
                        Rs. {{ number_format($netCashFlow) }}
                    </h3>
                    <p class="text-xs text-emerald-100/80 mt-1">Total Received - (Salaries + Expenses)</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center group-hover:scale-105 transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($netCashFlow >= 0)
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        @endif
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-between text-[11px] text-white/90">
                <span>Net Business Profit (Sales - Outflow):</span>
                <span class="font-bold">Rs. {{ number_format($netProfit) }}</span>
            </div>
        </div>

    </div>

    {{-- ================= SUMMARY OVERVIEW STRIP ================= --}}
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-lg print:hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-indigo-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Business Done (Billed)</span>
                    <h4 class="text-2xl font-bold text-white">Rs. {{ number_format($totalOrderAmount) }}</h4>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Money Collected</span>
                    <h4 class="text-2xl font-bold text-emerald-400">Rs. {{ number_format($totalReceipts) }}</h4>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-500/20 border border-rose-400/30 flex items-center justify-center text-rose-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Money Spent</span>
                    <h4 class="text-2xl font-bold text-rose-400">Rs. {{ number_format($totalOutflow) }}</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= DETAILED BREAKDOWN TABS ================= --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        
        {{-- Tab Headers --}}
        <div class="flex border-b border-slate-200 bg-slate-50/70 px-4 pt-3 gap-2 overflow-x-auto print:hidden">
            <button type="button" @click="activeTab = 'orders'"
                :class="activeTab === 'orders' ? 'border-indigo-600 text-indigo-600 bg-white shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                class="px-4 py-2.5 text-sm rounded-t-xl border-b-2 transition-all flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span>Orders List</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-200 text-slate-700 font-semibold">{{ $totalOrdersCount }}</span>
            </button>

            <button type="button" @click="activeTab = 'receipts'"
                :class="activeTab === 'receipts' ? 'border-emerald-600 text-emerald-600 bg-white shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                class="px-4 py-2.5 text-sm rounded-t-xl border-b-2 transition-all flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Receipts / Payments</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-200 text-slate-700 font-semibold">{{ $receiptsCount }}</span>
            </button>

            <button type="button" @click="activeTab = 'salaries'"
                :class="activeTab === 'salaries' ? 'border-rose-600 text-rose-600 bg-white shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                class="px-4 py-2.5 text-sm rounded-t-xl border-b-2 transition-all flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" />
                </svg>
                <span>Salaries Paid</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-200 text-slate-700 font-semibold">{{ $salariesCount }}</span>
            </button>

            <button type="button" @click="activeTab = 'expenses'"
                :class="activeTab === 'expenses' ? 'border-red-600 text-red-600 bg-white shadow-sm font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                class="px-4 py-2.5 text-sm rounded-t-xl border-b-2 transition-all flex items-center gap-2 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Expenses List</span>
                <span class="px-2 py-0.5 text-xs rounded-full bg-slate-200 text-slate-700 font-semibold">{{ $expensesCount }}</span>
            </button>
        </div>

        {{-- TAB 1: ORDERS --}}
        <div x-show="activeTab === 'orders'" class="p-0">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-800 text-base">Orders Placed ({{ $totalOrdersCount }})</h4>
                    <p class="text-xs text-slate-500">Orders registered between {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} and {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Orders Total:</span>
                    <span class="text-sm font-bold text-indigo-600 ml-1">Rs. {{ number_format($totalOrderAmount) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 text-left">Order #</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-left">Service</th>
                            <th class="px-4 py-3 text-center">Date</th>
                            <th class="px-4 py-3 text-right">Total Amount</th>
                            <th class="px-4 py-3 text-right">Paid</th>
                            <th class="px-4 py-3 text-right">Remaining</th>
                            <th class="px-4 py-3 text-center">Payment</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($ordersList as $order)
                        @php
                            $orderTotal = $order->totalAmount();
                            $remaining = $order->remainingDue();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-indigo-600">
                                <a href="{{ route('orders.invoice', $order->id) }}" class="hover:underline flex items-center gap-1">
                                    <span>#{{ str_pad($order->order_number ?? $order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <svg class="w-3.5 h-3.5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                </a>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-semibold text-slate-800">{{ $order->customer?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $order->customer?->phone ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-700">
                                <span>{{ $order->service?->name ?? '—' }}</span>
                                @if($order->quantity > 1)
                                <span class="text-xs text-slate-400">({{ $order->quantity }} pcs)</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                {{ optional($order->order_date)->format('d M Y') ?? optional($order->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold text-slate-800">
                                Rs. {{ number_format($orderTotal) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-medium text-emerald-600">
                                Rs. {{ number_format($order->paid_amount) }}
                            </td>
                            <td class="px-4 py-3.5 text-right font-bold {{ $remaining > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                Rs. {{ number_format($remaining) }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($order->payment_status === 'paid')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Paid</span>
                                @elseif($order->payment_status === 'partial')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Partial</span>
                                @else
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full bg-rose-50 text-rose-700 border border-rose-200">Unpaid</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-full 
                                    {{ $order->status === 'completed' || $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" /></svg>
                                </div>
                                <p class="text-sm font-medium">No orders found in this date range.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 2: RECEIPTS / PAYMENTS --}}
        <div x-show="activeTab === 'receipts'" class="p-0">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-800 text-base">Payment Receipts Collected ({{ $receiptsCount }})</h4>
                    <p class="text-xs text-slate-500">Payments collected between {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} and {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Total Collected:</span>
                    <span class="text-sm font-bold text-emerald-600 ml-1">Rs. {{ number_format($totalReceipts) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 text-left">Receipt ID</th>
                            <th class="px-4 py-3 text-left">Order #</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-center">Date & Time</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-4 py-3 text-left">Account</th>
                            <th class="px-4 py-3 text-left">Notes</th>
                            <th class="px-5 py-3 text-right">Amount Received</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($paymentsList as $payment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-700">
                                #REC-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-indigo-600">
                                @if($payment->order)
                                <a href="{{ route('orders.invoice', $payment->order->id) }}" class="hover:underline">
                                    #{{ str_pad($payment->order->order_number ?? $payment->order->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                                @else
                                —
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-medium text-slate-800">
                                {{ $payment->order?->customer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                {{ optional($payment->created_at)->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-slate-100 text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $payment->method)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 text-xs">
                                {{ $payment->account?->name ?? 'General / Cash' }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500 max-w-xs truncate">
                                {{ $payment->notes ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-emerald-600 text-base">
                                Rs. {{ number_format($payment->amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2" /></svg>
                                </div>
                                <p class="text-sm font-medium">No payment receipts found in this date range.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 3: SALARIES --}}
        <div x-show="activeTab === 'salaries'" class="p-0">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-800 text-base">Salaries Paid ({{ $salariesCount }})</h4>
                    <p class="text-xs text-slate-500">Salaries disbursed between {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} and {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Total Salaries:</span>
                    <span class="text-sm font-bold text-rose-600 ml-1">Rs. {{ number_format($totalSalariesPaid) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 text-left">Employee</th>
                            <th class="px-4 py-3 text-left">Department / Role</th>
                            <th class="px-4 py-3 text-center">Period / Month</th>
                            <th class="px-4 py-3 text-center">Paid Date</th>
                            <th class="px-4 py-3 text-right">Basic</th>
                            <th class="px-4 py-3 text-right">Bonus</th>
                            <th class="px-4 py-3 text-right">Deductions</th>
                            <th class="px-4 py-3 text-left">Method</th>
                            <th class="px-5 py-3 text-right">Net Paid Salary</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($salariesList as $salary)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-800">{{ $salary->employee?->name ?? '—' }}</div>
                                <div class="text-xs text-slate-400">{{ $salary->employee?->phone ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-600">
                                <div>{{ $salary->employee?->department?->name ?? 'General' }}</div>
                                <div class="text-slate-400">{{ $salary->employee?->designation?->name ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                @if($salary->from_date && $salary->to_date)
                                {{ optional($salary->from_date)->format('d M') }} — {{ optional($salary->to_date)->format('d M Y') }}
                                @else
                                {{ $salary->salary_month ?? '—' }}
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                {{ optional($salary->payment_date)->format('d M Y') ?? optional($salary->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-slate-700">
                                Rs. {{ number_format($salary->basic_salary) }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-emerald-600 text-xs">
                                +Rs. {{ number_format($salary->bonus) }}
                            </td>
                            <td class="px-4 py-3.5 text-right text-rose-600 text-xs">
                                -Rs. {{ number_format($salary->deductions) }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700">
                                    {{ ucfirst($salary->payment_method ?? 'cash') }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-rose-600 text-base">
                                Rs. {{ number_format($salary->net_salary) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0" /></svg>
                                </div>
                                <p class="text-sm font-medium">No salary payments recorded in this date range.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB 4: EXPENSES --}}
        <div x-show="activeTab === 'expenses'" class="p-0">
            <div class="px-6 py-4 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-slate-800 text-base">Expenses Incurred ({{ $expensesCount }})</h4>
                    <p class="text-xs text-slate-500">Expenses between {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} and {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-500">Total Expenses:</span>
                    <span class="text-sm font-bold text-red-600 ml-1">Rs. {{ number_format($totalExpenses) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 text-left">Voucher #</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-left">Paid To</th>
                            <th class="px-4 py-3 text-center">Expense Date</th>
                            <th class="px-4 py-3 text-left">Account</th>
                            <th class="px-4 py-3 text-left">Payment Method</th>
                            <th class="px-4 py-3 text-left">Notes / Ref</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($expensesList as $expense)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-700">
                                {{ $expense->voucher_no ?? ('EV-' . str_pad($expense->id, 4, '0', STR_PAD_LEFT)) }}
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-red-50 text-red-700 border border-red-100 text-xs font-semibold">
                                    {{ $expense->category }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-slate-700">
                                {{ $expense->paid_to ?? '—' }}
                            </td>
                            <td class="px-4 py-3.5 text-center text-xs text-slate-600">
                                {{ optional($expense->date)->format('d M Y') ?? optional($expense->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-600">
                                {{ $expense->account?->name ?? 'General / Cash' }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-slate-100 text-slate-700">
                                    {{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-xs text-slate-500 max-w-xs truncate">
                                {{ $expense->notes ?? $expense->reference_no ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-red-600 text-base">
                                Rs. {{ number_format($expense->amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                </div>
                                <p class="text-sm font-medium">No expenses recorded in this date range.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ================= ROW 3: CATEGORY & PAYMENT METHOD BREAKDOWNS ================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:hidden">

        {{-- Payment Methods Breakdown --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h4 class="font-bold text-slate-800 text-base flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <span>Receipts by Payment Method</span>
            </h4>
            @if($receiptsByMethod->count() > 0)
            <div class="space-y-3">
                @foreach($receiptsByMethod as $method)
                @php
                    $pct = $totalReceipts > 0 ? round(($method->total / $totalReceipts) * 100, 1) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700 capitalize">{{ str_replace('_', ' ', $method->method) }} ({{ $method->count }} txns)</span>
                        <span class="text-emerald-700 font-bold">Rs. {{ number_format($method->total) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-6">No payment records in selected period.</p>
            @endif
        </div>

        {{-- Expenses by Category Breakdown --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <h4 class="font-bold text-slate-800 text-base flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <span>Expenses by Category</span>
            </h4>
            @if($expensesByCategory->count() > 0)
            <div class="space-y-3">
                @foreach($expensesByCategory as $expCat)
                @php
                    $pct = $totalExpenses > 0 ? round(($expCat->total / $totalExpenses) * 100, 1) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                        <span class="text-slate-700">{{ $expCat->category }} ({{ $expCat->count }} vouchers)</span>
                        <span class="text-red-700 font-bold">Rs. {{ number_format($expCat->total) }} ({{ $pct }}%)</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="bg-red-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-slate-400 text-center py-6">No expense records in selected period.</p>
            @endif
        </div>

    </div>

</div>

{{-- Print-friendly styles --}}
<style>
@media print {
    body {
        background: white !important;
        overflow: visible !important;
        height: auto !important;
    }
    aside, #sidebar, header, nav, button, .print\:hidden {
        display: none !important;
    }
    main {
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
    }
    .print\:block {
        display: block !important;
    }
    table {
        page-break-inside: auto;
    }
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}
</style>
@endsection
