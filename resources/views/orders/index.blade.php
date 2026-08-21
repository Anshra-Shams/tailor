@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-slate-800">Orders</h2>
        <p class="text-sm text-slate-500">Manage all tailor orders</p>
    </div>
    <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        New Order
    </a>
</div>

@php
    $statusColors = [
        'pending'     => 'bg-amber-50 text-amber-700 border-amber-200',
        'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
        'completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'cancelled'   => 'bg-red-50 text-red-600 border-red-200',
    ];
    $statusLabels = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
    ];
@endphp

@if ($orders->isEmpty())
    <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
        <p class="text-slate-500 font-medium">No orders yet</p>
        <a href="{{ route('orders.create') }}" class="mt-3 inline-block text-sm text-indigo-600 font-semibold hover:underline">Create your first order →</a>
    </div>
@else

    {{-- MOBILE: Card Layout --}}
    <div class="space-y-4 md:hidden">
        @foreach ($orders as $order)
            <div class="block bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-mono text-xs text-slate-400">#{{ $order->id }}</span>
                    <span class="px-2.5 py-0.5 text-xs font-semibold rounded-lg border {{ $statusColors[$order->status] ?? '' }}">
                        {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <span class="text-sm font-semibold text-slate-800">{{ $order->customer->name ?? '—' }}</span>
                        @if ($order->member)
                            <span class="text-xs text-slate-400">/</span>
                            <span class="text-sm text-slate-500">{{ $order->member->name }}</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" /></svg>
                        <span class="text-sm text-slate-600">{{ $order->service->name ?? '—' }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
                    <span class="text-sm font-bold text-slate-800">Rs {{ number_format($order->price) }}</span>
                    <span class="text-xs text-slate-400">{{ $order->order_date?->format('M d, Y') ?? '—' }}</span>
                </div>

                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                    <a href="{{ route('orders.show', $order) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        View
                    </a>
                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order?')" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    {{-- DESKTOP: Table Layout --}}
    <div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50">
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">#</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Customer</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Member</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Service</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Price</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Status</th>
                        <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Date</th>
                        <th class="text-right px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($orders as $order)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-5 py-3 font-mono text-slate-500 text-xs whitespace-nowrap">#{{ $order->id }}</td>
                            <td class="px-5 py-3 font-medium text-slate-700 whitespace-nowrap">{{ $order->customer->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $order->member->name ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $order->service->name ?? '—' }}</td>
                            <td class="px-5 py-3 font-medium text-slate-700 whitespace-nowrap">Rs {{ number_format($order->price) }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-lg border {{ $statusColors[$order->status] ?? '' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $order->order_date?->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('orders.show', $order) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

@endif
@endsection
