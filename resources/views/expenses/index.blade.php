@extends('layouts.app')

@section('title', 'Expense Voucher')

@section('content')
<div class="space-y-6" x-data="{ addModalOpen: false }">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Expense Voucher</h2>
            <p class="text-slate-500 mt-1">Record and manage company business expenses</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            {{-- Stats --}}
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-center min-w-[110px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Today</p>
                <p class="text-lg font-bold text-rose-600">Rs. {{ number_format($todayTotal) }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-center min-w-[120px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">This Month</p>
                <p class="text-lg font-bold text-slate-800">Rs. {{ number_format($monthTotal) }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-center min-w-[120px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Total Expenses</p>
                <p class="text-lg font-bold text-indigo-600">Rs. {{ number_format($allTotal) }}</p>
            </div>

            <a href="{{ route('expense-categories.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Categories
            </a>

            {{-- New Expense Voucher Button --}}
            <button type="button" @click="addModalOpen = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                New Expense Voucher
            </button>
        </div>
    </div>

    {{-- Filters Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-3.5 shadow-sm">
        <form method="GET" action="{{ route('expenses.index') }}" class="flex items-center gap-3 w-full flex-nowrap" style="display: flex; flex-direction: row; align-items: center; gap: 12px; width: 100%; flex-wrap: nowrap;">
            {{-- Search text (compact) --}}
            <div class="relative w-64 flex-shrink-0" style="width: 240px; flex-shrink: 0;">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search vouchers..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Category Filter --}}
            <div class="w-44 flex-shrink-0" style="width: 175px; flex-shrink: 0;">
                <select name="expense_category_id" class="w-full py-2 px-3 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div class="flex items-center gap-1.5 border border-slate-200 rounded-xl px-3 py-2 bg-white flex-shrink-0" style="display: flex; align-items: center; gap: 6px; padding: 7px 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; flex-shrink: 0;">
                <span class="text-xs text-slate-400 font-semibold whitespace-nowrap">From:</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="text-xs text-slate-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none"
                    style="border: none !important; outline: none !important; box-shadow: none !important; padding: 0 !important; background: transparent !important;">
            </div>

            {{-- Date To --}}
            <div class="flex items-center gap-1.5 border border-slate-200 rounded-xl px-3 py-2 bg-white flex-shrink-0" style="display: flex; align-items: center; gap: 6px; padding: 7px 12px; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; flex-shrink: 0;">
                <span class="text-xs text-slate-400 font-semibold whitespace-nowrap">To:</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="text-xs text-slate-700 bg-transparent border-0 p-0 focus:ring-0 focus:outline-none"
                    style="border: none !important; outline: none !important; box-shadow: none !important; padding: 0 !important; background: transparent !important;">
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0" style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl transition shadow-sm whitespace-nowrap">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'expense_category_id', 'date_from', 'date_to']))
                    <a href="{{ route('expenses.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold rounded-xl transition whitespace-nowrap">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Expenses Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-700">All Expense Vouchers</h3>
            <span class="text-xs text-slate-400">{{ $expenses->total() }} record(s) found</span>
        </div>

        @if($expenses->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-rose-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-1.125 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-600">No expense vouchers found</h4>
                <p class="text-xs text-slate-400 mt-1">Click "New Expense Voucher" to record a new business expense.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Voucher #</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Date</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Category / Title</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Paid From</th>
                            <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Amount</th>
                            <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($expenses as $exp)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        {{ $exp->voucher_no ?? ('EV-' . str_pad($exp->id, 4, '0', STR_PAD_LEFT)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-slate-700 font-medium text-xs">{{ $exp->date ? $exp->date->format('d M Y') : '—' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $exp->created_at->format('h:i A') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800 text-sm">{{ $exp->expenseCategory?->name ?? $exp->category }}</p>
                                    @if($exp->notes)
                                        <p class="text-[11px] text-slate-400 line-clamp-1" title="{{ $exp->notes }}">{{ $exp->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-xs font-semibold text-slate-700">{{ $exp->account?->name ?? '—' }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 uppercase tracking-wider">{{ str_replace('_', ' ', $exp->payment_method) }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <span class="font-bold text-rose-600 text-sm">Rs. {{ number_format($exp->amount, 2) }}</span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <form action="{{ route('expenses.destroy', $exp) }}" method="POST" class="inline-block js-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                            title="Delete voucher">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-5 py-3 border-t border-slate-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         NEW EXPENSE VOUCHER MODAL
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="addModalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="addModalOpen = false"></div>

        {{-- Modal content --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">New Expense Voucher</h3>
                        <p class="text-xs text-slate-400">Record a payment or business expense</p>
                    </div>
                </div>
                <button type="button" @click="addModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                {{-- Date & Amount row --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Voucher Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Amount (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00"
                            class="w-full text-sm font-semibold border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Account (Paid From) --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Paid From Account <span class="text-red-500">*</span></label>
                    <select name="account_id" required
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Account</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">
                                {{ $acc->name }} ({{ $acc->category?->name ?? 'Account' }}) — Balance: Rs. {{ number_format($acc->current_balance) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Expense Category / Head --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-600">Expense Category <span class="text-red-500">*</span></label>
                        <a href="{{ route('expense-categories.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center gap-1">
                            <span>+ Manage Categories</span>
                        </a>
                    </div>
                    <select name="expense_category_id" required
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Select Expense Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Description / Notes --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Notes / Description</label>
                    <textarea name="notes" rows="2" placeholder="Additional details about this expense..."
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="button" @click="addModalOpen = false"
                        class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                        Save Voucher
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
