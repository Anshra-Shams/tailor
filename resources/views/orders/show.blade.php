@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('orders.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </a>
    <div>
        <h2 class="text-xl font-bold text-slate-800">Order #{{ $order->id }}</h2>
        <p class="text-sm text-slate-500">{{ $order->service->name ?? '—' }} for {{ $order->customer->name ?? '—' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Details --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Status --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5" x-data="{ status: '{{ $order->status }}' }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Status</h3>
                @php
                    $statusColors = [
                        'pending'     => 'bg-amber-100 text-amber-700',
                        'in_progress' => 'bg-blue-100 text-blue-700',
                        'completed'   => 'bg-emerald-100 text-emerald-700',
                        'cancelled'   => 'bg-red-100 text-red-600',
                    ];
                @endphp
                <span class="px-3 py-1 text-xs font-bold rounded-lg {{ $statusColors[$order->status] ?? '' }}"
                      x-text="status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())"></span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach(['pending','in_progress','completed','cancelled'] as $s)
                    <form method="POST" action="{{ route('orders.updateStatus', $order) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $s }}">
                        <button type="submit"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg border-2 transition {{ $order->status === $s ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 text-slate-500 hover:border-slate-300' }}">
                            {{ ucwords(str_replace('_', ' ', $s)) }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- Measurements --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Measurements</h3>
            @if (!empty($measurements))
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($measurements as $key => $val)
                        <div class="bg-slate-50 rounded-lg px-3 py-2">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                            <div class="text-sm font-semibold text-slate-700 mt-0.5">{{ $val }} <span class="text-slate-400 font-normal text-xs">in</span></div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400">No measurements recorded.</p>
            @endif
        </div>

        {{-- Notes --}}
        @if ($order->notes)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-semibold text-slate-800 mb-2">Notes</h3>
                <p class="text-sm text-slate-600">{{ $order->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Customer</div>
                <a href="{{ route('customers.show', $order->customer_id) }}" class="text-sm font-semibold text-indigo-600 hover:underline mt-0.5 block">
                    {{ $order->customer->name ?? '—' }}
                </a>
            </div>
            @if ($order->member)
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Member</div>
                    <div class="text-sm font-medium text-slate-700 mt-0.5">{{ $order->member->name }}</div>
                </div>
            @endif
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Service</div>
                <div class="text-sm font-medium text-slate-700 mt-0.5">{{ $order->service->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Price</div>
                <div class="text-lg font-bold text-slate-800 mt-0.5">Rs {{ number_format($order->price) }}</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Order Date</div>
                    <div class="text-sm text-slate-700 mt-0.5">{{ $order->order_date?->format('M d, Y') ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Due Date</div>
                    <div class="text-sm text-slate-700 mt-0.5">{{ $order->due_date?->format('M d, Y') ?? '—' }}</div>
                </div>
            </div>
            @if ($order->completed_date)
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Completed</div>
                    <div class="text-sm text-emerald-600 font-medium mt-0.5">{{ $order->completed_date->format('M d, Y') }}</div>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4">
            <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('Delete this order permanently?')">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition">
                    Delete Order
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
