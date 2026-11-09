@extends('layouts.app')

@section('title', $account->name . ' — Ledger')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('accounts.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-500 transition-all flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div class="min-w-0">
                <h2 class="text-2xl font-bold text-slate-800 truncate">{{ $account->name }}</h2>
                <p class="text-slate-500 mt-0.5 text-sm">
                    {{ $account->category?->name ?? 'Account' }} &middot; Account Ledger
                </p>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[110px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Total In</p>
                <p class="text-base font-bold text-emerald-600">+ Rs. {{ number_format($totalIn ?? 0, 2) }}</p>
            </div>
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2 text-center min-w-[110px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Total Out</p>
                <p class="text-base font-bold text-rose-600">- Rs. {{ number_format($totalOut ?? 0, 2) }}</p>
            </div>
            <div class="bg-indigo-50 border border-indigo-100 rounded-xl px-4 py-2 text-center min-w-[130px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-indigo-500">Current Balance</p>
                <p class="text-lg font-bold text-indigo-700">Rs. {{ number_format($account->current_balance, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Ledger table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">

        @if ($rows->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-600">No transactions found</h4>
                <p class="text-xs text-slate-400 mt-1">No receipts or expenses have been recorded for this account yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 w-28">Ref #</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 w-36">Type</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Particulars / Details</th>
                            <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Amount</th>
                            <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($rows as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                {{-- Ref # --}}
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    @if($row['type'] === 'expense')
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 px-2.5 rounded-lg bg-rose-100 text-rose-700 font-bold text-[11px] border border-rose-200">
                                            {{ $row['ref_no'] }}
                                        </span>
                                    @elseif($row['type'] === 'salary')
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 px-2.5 rounded-lg bg-purple-100 text-purple-700 font-bold text-[11px] border border-purple-200">
                                            {{ $row['ref_no'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 px-2.5 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-[11px]">
                                            {{ $row['ref_no'] }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Type Badge --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($row['type'] === 'expense')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                            Expense Voucher
                                        </span>
                                    @elseif($row['type'] === 'salary')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            Salary Payment
                                        </span>
                                    @elseif($row['badge'] === 'Advance')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Advance Payment
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Order Payment
                                        </span>
                                    @endif
                                </td>

                                {{-- Particulars / Details --}}
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-slate-800 text-sm">{{ $row['title'] }}</p>
                                    @if(!empty($row['subtitle']))
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ $row['subtitle'] }}</p>
                                    @endif
                                    @if(!empty($row['notes']) && $row['notes'] !== $row['subtitle'])
                                        <p class="text-[11px] text-slate-500 italic mt-0.5">{{ $row['notes'] }}</p>
                                    @endif
                                </td>

                                {{-- Amount (In vs Out) --}}
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    @if($row['direction'] === 'in')
                                        <span class="font-bold text-emerald-600 text-sm">
                                            + Rs. {{ number_format($row['amount'], 2) }}
                                        </span>
                                    @else
                                        <span class="font-bold text-rose-600 text-sm">
                                            - Rs. {{ number_format($row['amount'], 2) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Date & Time --}}
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <p class="text-slate-700 text-xs font-medium">{{ $row['date'] }}</p>
                                    <p class="text-slate-400 text-[11px]">{{ $row['time'] }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
