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
                    {{ $account->category?->name ?? 'Account' }} &middot; Payment Ledger
                </p>
            </div>
        </div>
    </div>

    {{-- Ledger table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        @if ($rows->isEmpty())
            <div class="px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-semibold text-slate-600">No payments found</h4>
                <p class="text-xs text-slate-400 mt-1">No payments have been recorded for this account yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Order #</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Customer</th>
                            <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Amount</th>
                            <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($rows as $row)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center justify-center min-w-[2.5rem] h-8 px-2 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-[10px]">
                                        {{ $row['order_no'] ? '#' . $row['order_no'] : '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-slate-800 text-sm">{{ $row['customer'] }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $row['phone'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-emerald-700 whitespace-nowrap">Rs. {{ number_format($row['amount'], 2) }}</span>
                                </td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    <p class="text-slate-700 text-[12px]">{{ $row['date'] }}</p>
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
