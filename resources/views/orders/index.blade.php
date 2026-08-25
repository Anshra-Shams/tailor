@extends('layouts.app')

@section('title', 'Orders')

@php
    $statusColors = [
        'pending'     => 'bg-amber-50 text-amber-700 border-amber-200',
        'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
        'completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'delivered'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'cancelled'   => 'bg-red-50 text-red-700 border-red-200',
    ];
@endphp

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Orders</h2>
            <p class="text-slate-500 mt-1">{{ $orders->total() }} order{{ $orders->total() == 1 ? '' : 's' }} total</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create Order
        </a>
    </div>

    {{-- Flash --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0" class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-medium text-emerald-800 flex-1">{{ session('success') }}</p>
            <button type="button" @click="show = false" class="text-emerald-400 hover:text-emerald-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Orders table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        @if ($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left">
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">#</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Customer</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Service</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Total</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Paid</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Delivery</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $o)
                            @php
                                $total = (float) $o->price * (int) $o->quantity;
                                $overdue = $o->status === 'pending' && $o->due_date->isPast();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-4 py-3.5 text-slate-400 font-medium whitespace-nowrap">{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3.5 min-w-[160px]">
                                    <div class="font-semibold text-slate-800 truncate max-w-[180px]">{{ $o->customer?->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        @if ($o->member)
                                            {{ $o->member->name }}@if($o->member->relation) <span class="capitalize">({{ $o->member->relation }})</span>@endif
                                        @else
                                            Self
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="text-slate-700 font-medium">{{ $o->service?->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">Qty: {{ $o->quantity }} &middot; Rs. {{ number_format((float) $o->price) }}/pc</div>
                                </td>
                                <td class="px-4 py-3.5 text-right font-semibold text-slate-800 whitespace-nowrap">Rs. {{ number_format($total) }}</td>
                                <td class="px-4 py-3.5 text-right font-medium text-emerald-600 whitespace-nowrap">{{ (float) $o->paid_amount > 0 ? ('Rs. ' . number_format((float) $o->paid_amount)) : '—' }}</td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="@if($overdue) text-red-600 font-semibold @else text-slate-600 @endif">{{ $o->due_date?->format('d M Y') }}</span>
                                    @if ($overdue)
                                        <span class="ml-1 text-[10px] uppercase font-bold text-red-500 bg-red-50 border border-red-100 rounded px-1 py-0.5">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-semibold capitalize whitespace-nowrap {{ $statusColors[$o->status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                                        {{ str_replace('_', ' ', $o->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages())
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                    {{ $orders->withQueryString()->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <p class="text-slate-500 font-medium">No orders yet</p>
                <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">Create your first order</a>
            </div>
        @endif
    </div>
</div>
@endsection
