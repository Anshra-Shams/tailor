@extends('layouts.app')

@section('title', 'Orders')

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
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Actions</th>
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
                                <td class="px-4 py-3.5" x-data="statusCell('{{ $o->status }}', '{{ route('orders.updateStatus', $o) }}')">
                                    <button type="button" @click.stop="toggle($event)" :class="cls" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-semibold capitalize whitespace-nowrap hover:brightness-95 transition-all cursor-pointer">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                        <span x-text="label"></span>
                                        <svg class="w-3 h-3 opacity-60 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                                    </button>

                                    <div x-show="open" @click.outside="open = false"
                                             class="fixed z-[70] bg-white border border-slate-200 rounded-xl shadow-xl py-1 w-44 overflow-hidden"
                                             :style="'top:' + panelY + 'px; left:' + panelX + 'px'"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             style="display:none">
                                            <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">Update Status</div>
                                            <template x-for="s in options" :key="s.key">
                                                <button type="button" @click.stop="pick(s.key)" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-left hover:bg-indigo-50 transition capitalize" :class="s.key === status ? 'text-indigo-600 bg-indigo-50/70' : 'text-slate-600'">
                                                    <span class="w-2 h-2 rounded-full flex-shrink-0" :class="s.dot"></span>
                                                    <span class="flex-1" x-text="s.label"></span>
                                                    <svg x-show="s.key === status" class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                                </button>
                                            </template>
                                        </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('orders.create', ['edit' => $o->id]) }}" title="Edit order" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('orders.destroy', $o) }}" class="js-delete-form" data-name="order #{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}" data-title="Delete this order?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete order" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition">
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

@push('scripts')
<script>
function statusCell(initial, url) {
    return {
        url: url,
        status: initial,
        busy: false,
        open: false,
        panelX: 0,
        panelY: 0,
        options: [
            { key: 'pending',     label: 'Pending',     dot: 'bg-amber-400' },
            { key: 'in_progress', label: 'In Progress', dot: 'bg-blue-400' },
            { key: 'completed',   label: 'Completed',   dot: 'bg-emerald-400' },
            { key: 'delivered',   label: 'Delivered',   dot: 'bg-indigo-400' },
            { key: 'cancelled',   label: 'Cancelled',   dot: 'bg-red-400' },
        ],
        get label() {
            return this.options.find(o => o.key === this.status)?.label || this.status;
        },
        get cls() {
            const m = {
                pending:     'bg-amber-50 text-amber-700 border-amber-200',
                in_progress: 'bg-blue-50 text-blue-700 border-blue-200',
                completed:   'bg-emerald-50 text-emerald-700 border-emerald-200',
                delivered:   'bg-indigo-50 text-indigo-700 border-indigo-200',
                cancelled:   'bg-red-50 text-red-700 border-red-200',
            };
            return m[this.status] || 'bg-slate-50 text-slate-600 border-slate-200';
        },
        init() {
            this._close = () => { this.open = false; };
            window.addEventListener('scroll', this._close, true);
            window.addEventListener('resize', this._close);
        },
        destroy() {
            window.removeEventListener('scroll', this._close, true);
            window.removeEventListener('resize', this._close);
        },
        toggle(evt) {
            if (this.busy) return;
            const r = evt.currentTarget.getBoundingClientRect();
            const W = 176, H = 230;
            this.panelX = Math.min(r.left, window.innerWidth - W - 12);
            this.panelY = (window.innerHeight - r.bottom > H + 16)
                ? r.bottom + 6
                : Math.max(12, r.top - H - 6);
            this.open = !this.open;
        },
        async pick(key) {
            if (key === this.status || this.busy) { this.open = false; return; }
            this.busy = true;
            try {
                const fd = new FormData();
                fd.append('_method', 'PATCH');
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                fd.append('status', key);
                const r = await fetch(this.url, { method: 'POST', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }, body: fd });
                if (!r.ok) throw new Error('failed');
                const d = await r.json();
                this.status = d.status;
                if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Status updated', showConfirmButton: false, timer: 1500 });
            } catch (e) {
                if (window.Swal) Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Could not update status', showConfirmButton: false, timer: 2000 });
            }
            this.busy = false;
            this.open = false;
        }
    };
}
</script>
@endpush
