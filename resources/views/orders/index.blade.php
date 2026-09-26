@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="space-y-6" x-data="ordersIndexManager()">

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

    {{-- Search --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <form method="GET" action="{{ route('orders.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order ID, customer, member, service, or status..."
                    class="block w-full pl-11 pr-28 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                <div class="absolute inset-y-1.5 right-1.5 flex items-center gap-1.5">
                    @if(request('q'))
                        <a href="{{ route('orders.index') }}" title="Clear search" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    <button type="submit" class="px-4 h-9 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition cursor-pointer">Search</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Orders table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        @if ($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-left">
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">#</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Customer</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide">Services</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Total</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Paid</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">Order Date</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">Delivery</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $o)
                            @php
                                $items = $o->batch_items ?? collect([$o]);
                                $batchTotal = $items->sum(fn ($i) => (float) $i->price * (int) $i->quantity);
                                $batchPaid  = $items->sum(fn ($i) => (float) $i->paid_amount);
                                $batchDue   = max(0, $batchTotal - $batchPaid);
                                $overdue    = $o->status === 'pending' && $o->due_date && $o->due_date->isPast();

                                $modalItems = $items->map(function ($item) {
                                    $meas = is_string($item->measurements) ? json_decode($item->measurements, true) : (is_array($item->measurements) ? $item->measurements : []);
                                    
                                    // Build service fields map if defined on the service
                                    $serviceFieldsMap = [];
                                    if ($item->service && is_array($item->service->measurement_fields)) {
                                        foreach ($item->service->measurement_fields as $sf) {
                                            $fType = strtolower(trim($sf['type'] ?? 'upper'));
                                            if (!empty($sf['key'])) {
                                                $serviceFieldsMap[strtolower($sf['key'])] = $fType;
                                            }
                                            if (!empty($sf['label'])) {
                                                $serviceFieldsMap[strtolower($sf['label'])] = $fType;
                                                $serviceFieldsMap[strtolower(str_replace(' ', '_', $sf['label']))] = $fType;
                                            }
                                        }
                                    }

                                    $upperSpecs = [];
                                    $lowerSpecs = [];
                                    $lowerKeywords = ['shalwar', 'trouser', 'paincha', 'bottom', 'pant', 'lower', 'asan', 'fly', 'inseam', 'thigh', 'thai', 'knee', 'ankle', 'pajama', 'belt', 'elastic'];

                                    if (is_array($meas)) {
                                        foreach ($meas as $mk => $mv) {
                                            if ($mv !== null && $mv !== '' && !str_starts_with($mk, '__')) {
                                                $valStr = is_array($mv) ? implode(', ', $mv) : (string)$mv;
                                                $mkLower = strtolower($mk);

                                                $isLower = false;
                                                if (str_starts_with($mkLower, 'custom_l_')) {
                                                    $isLower = true;
                                                } elseif (str_starts_with($mkLower, 'custom_u_')) {
                                                    $isLower = false;
                                                } elseif (isset($serviceFieldsMap[$mkLower]) && $serviceFieldsMap[$mkLower] === 'lower') {
                                                    $isLower = true;
                                                } else {
                                                    foreach ($lowerKeywords as $kw) {
                                                        if (str_contains($mkLower, $kw)) {
                                                            $isLower = true;
                                                            break;
                                                        }
                                                    }
                                                }

                                                // Clean key label: strip custom_u_, custom_l_, custom_ prefixes & trailing _9465 / 9465 ID suffix
                                                $cleanKey = preg_replace('/^custom_[ul]_/i', '', $mk);
                                                $cleanKey = preg_replace('/^custom_/i', '', $cleanKey);
                                                $cleanKey = preg_replace('/_\d+$/', '', $cleanKey);
                                                $cleanKey = preg_replace('/\b(upper|lower)\b/i', '', $cleanKey);
                                                $cleanKey = trim(ucwords(preg_replace('/\s+/', ' ', str_replace('_', ' ', $cleanKey))));
                                                $cleanKey = preg_replace('/\s+\d+$/', '', $cleanKey);
                                                if (empty($cleanKey)) {
                                                    $cleanKey = ucwords(str_replace('_', ' ', $mk));
                                                }

                                                if ($isLower) {
                                                    $lowerSpecs[$cleanKey] = $valStr;
                                                } else {
                                                    $upperSpecs[$cleanKey] = $valStr;
                                                }
                                            }
                                        }
                                    }

                                    $cleanNote = trim(preg_replace('/(Standard|Premium|Basic|Urgent)?\s*Stitching/i', '', $item->notes ?? ''));
                                    $cleanNote = trim(preg_replace('/^[—\-\s]+|[—\-\s]+$/u', '', $cleanNote));

                                    return [
                                        'id'           => $item->id,
                                        'service_name' => $item->service?->name ?? 'Stitching Service',
                                        'note'         => $cleanNote,
                                        'price'        => (float) $item->price,
                                        'quantity'     => (int) $item->quantity,
                                        'total'        => (float) $item->price * (int) $item->quantity,
                                        'upper_specs'  => $upperSpecs,
                                        'lower_specs'  => $lowerSpecs,
                                        'specs'        => array_merge($upperSpecs, $lowerSpecs),
                                    ];
                                })->values();

                                $viewData = [
                                    'order_id'       => str_pad($o->id, 4, '0', STR_PAD_LEFT),
                                    'order_date'     => ($o->order_date ?? $o->created_at)?->format('d M Y, h:i A') ?? '—',
                                    'due_date'       => $o->due_date?->format('d M Y') ?? '—',
                                    'status'         => ucfirst(str_replace('_', ' ', $o->status)),
                                    'raw_status'     => $o->status,
                                    'customer_name'  => $o->customer?->name ?? '—',
                                    'customer_phone' => $o->customer?->phone ?? '—',
                                    'member_name'    => $o->member ? ($o->member->name . ($o->member->relation ? ' (' . ucfirst($o->member->relation) . ')' : '')) : 'Self',
                                    'invoice_url'    => route('orders.invoice', $o),
                                    'edit_url'       => route('orders.create', ['edit' => $o->id]),
                                    'items'          => $modalItems,
                                    'grand_total'    => $batchTotal,
                                    'total_paid'     => $batchPaid,
                                    'total_due'      => $batchDue,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-4 py-3.5 align-middle text-slate-400 font-medium whitespace-nowrap">{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-3.5 align-middle min-w-[150px]">
                                    <div class="font-bold text-slate-800 truncate max-w-[180px]">{{ $o->customer?->name ?? '—' }}</div>
                                    <div class="text-xs text-slate-400 mt-0.5">
                                        @if ($o->member)
                                            {{ $o->member->name }}@if($o->member->relation) <span class="capitalize">({{ $o->member->relation }})</span>@endif
                                        @else
                                            Self
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50/90 text-indigo-700 font-semibold text-xs border border-indigo-100/80">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v1.281m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        {{ $items->count() }} {{ $items->count() == 1 ? 'Service' : 'Services' }} in order
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 align-middle text-right font-bold text-slate-800 whitespace-nowrap">Rs. {{ number_format($batchTotal) }}</td>
                                <td class="px-4 py-3.5 align-middle text-right font-semibold text-emerald-600 whitespace-nowrap">{{ $batchPaid > 0 ? ('Rs. ' . number_format($batchPaid)) : '—' }}</td>
                                <td class="px-4 py-3.5 align-middle whitespace-nowrap">
                                    <div class="text-slate-700 font-medium text-xs">{{ ($o->order_date ?? $o->created_at)?->format('d M Y') ?? '—' }}</div>
                                    @if ($o->created_at)
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $o->created_at->format('h:i A') }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 align-middle whitespace-nowrap text-xs">
                                    <span class="@if($overdue) text-red-600 font-bold @else text-slate-700 font-medium @endif">{{ $o->due_date?->format('d M Y') }}</span>
                                    @if ($overdue)
                                        <span class="ml-1 text-[10px] uppercase font-bold text-red-500 bg-red-50 border border-red-100 rounded px-1 py-0.5">Overdue</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 align-middle whitespace-nowrap" x-data="statusCell('{{ $o->status }}', '{{ route('orders.updateStatus', $o) }}')">
                                    <button type="button" @click.stop="toggle($event)" :class="cls" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-semibold capitalize whitespace-nowrap hover:shadow-xs transition-all cursor-pointer">
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
                                            <button type="button" @click.stop="pick(s.key)" class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-left hover:bg-indigo-50 transition capitalize cursor-pointer" :class="s.key === status ? 'text-indigo-600 bg-indigo-50/70 font-semibold' : 'text-slate-600'">
                                                <span class="w-2 h-2 rounded-full flex-shrink-0" :class="s.dot"></span>
                                                <span class="flex-1" x-text="s.label"></span>
                                                <svg x-show="s.key === status" class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 align-middle whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- View Details Modal Eye Icon Button --}}
                                        <button type="button" @click="openViewModal({{ json_encode($viewData) }})" title="View Order Details & Services" class="w-8 h-8 flex items-center justify-center rounded-lg border border-indigo-200/80 bg-indigo-50/80 text-indigo-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition shadow-2xs cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                        <a href="{{ route('orders.invoice', $o) }}" title="View / Print Invoice" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656"/></svg>
                                        </a>
                                        <a href="{{ route('orders.create', ['edit' => $o->id]) }}" title="Edit order" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('orders.destroy', $o) }}" class="js-delete-form" data-name="order #{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}" data-title="Delete this order batch?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete order batch" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition shadow-2xs cursor-pointer">
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
                <p class="text-slate-500 font-medium">{{ request('q') ? 'No orders match your search' : 'No orders yet' }}</p>
                @if(request('q'))
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">Clear search</a>
                @else
                    <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">Create your first order</a>
                @endif
            </div>
        @endif
    </div>

    {{-- Order Details View Modal --}}
    <div x-show="viewModalOpen" x-transition.opacity class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="closeViewModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl z-10 max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-indigo-600 text-white font-bold text-xs rounded-lg" x-text="'Order #' + (viewData?.order_id || '')"></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-800" x-text="viewData?.customer_name"></h3>
                        <p class="text-xs text-slate-400" x-text="'Order Date: ' + (viewData?.order_date || '')"></p>
                    </div>
                </div>
                <button type="button" @click="closeViewModal()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5 overflow-y-auto max-h-[calc(90vh-140px)]">
                
                {{-- Customer & Dates Summary Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    {{-- Customer Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Customer</span>
                            <span class="font-bold text-slate-800 text-sm block truncate" x-text="viewData?.customer_name"></span>
                            <span class="text-slate-500 font-medium text-xs block truncate mt-0.5" x-text="viewData?.customer_phone"></span>
                        </div>
                    </div>

                    {{-- Order For Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Order For</span>
                            <span class="font-bold text-slate-800 text-sm block truncate" x-text="viewData?.member_name"></span>
                        </div>
                    </div>

                    {{-- Delivery Due Date Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3.5 flex items-start gap-3">
                        <div class="p-2 rounded-lg bg-amber-50 text-amber-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Delivery Due Date</span>
                            <span class="font-bold text-amber-700 text-sm block truncate" x-text="viewData?.due_date"></span>
                        </div>
                    </div>
                </div>

                {{-- Services List --}}
                <div>
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5">Services & Specs</h4>
                    <div class="space-y-3">
                        <template x-for="(item, idx) in (viewData?.items || [])" :key="'vitem_'+idx">
                            <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-2xs space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 font-bold text-xs flex items-center justify-center" x-text="idx + 1"></span>
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-800" x-text="item.service_name"></h5>
                                            <p x-show="item.note" class="text-xs text-amber-700 font-medium mt-0.5" x-text="'Note: ' + item.note"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-slate-800 block" x-text="'Rs. ' + (item.total || 0).toLocaleString()"></span>
                                        <span class="text-xs text-slate-400 block" x-text="item.quantity + ' x Rs. ' + (item.price || 0).toLocaleString()"></span>
                                    </div>
                                </div>

                                {{-- Specs / Measurements --}}
                                <template x-if="(Object.keys(item.upper_specs || {}).length > 0) || (Object.keys(item.lower_specs || {}).length > 0) || (Object.keys(item.specs || {}).length > 0)">
                                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                                        {{-- Upper Body Measurements --}}
                                        <template x-if="Object.keys(item.upper_specs || {}).length > 0">
                                            <div>
                                                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">
                                                    👕 Upper Body Measurements:
                                                </span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="(val, label) in item.upper_specs" :key="'up_'+label">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-indigo-50/70 text-slate-800 text-xs font-medium border border-indigo-100/80">
                                                            <strong class="font-semibold text-indigo-950" x-text="label + ':'"></strong>
                                                            <span x-text="val"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Lower Body Measurements --}}
                                        <template x-if="Object.keys(item.lower_specs || {}).length > 0">
                                            <div>
                                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">
                                                    👖 Lower Body Measurements:
                                                </span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="(val, label) in item.lower_specs" :key="'lw_'+label">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-emerald-50/70 text-slate-800 text-xs font-medium border border-emerald-100/80">
                                                            <strong class="font-semibold text-emerald-950" x-text="label + ':'"></strong>
                                                            <span x-text="val"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        {{-- Fallback if neither upper nor lower specs exist but specs exist --}}
                                        <template x-if="!Object.keys(item.upper_specs || {}).length && !Object.keys(item.lower_specs || {}).length && Object.keys(item.specs || {}).length">
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Measurements & Specifications:</span>
                                                <div class="flex flex-wrap gap-1.5">
                                                    <template x-for="(val, label) in item.specs" :key="'sp_'+label">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200/60">
                                                            <strong class="font-semibold text-slate-900" x-text="label + ':'"></strong>
                                                            <span x-text="val"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Financial Summary Bar --}}
                <div class="bg-slate-900 text-white rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-6">
                        <div>
                            <span class="text-slate-400 block">TOTAL AMOUNT</span>
                            <span class="text-base font-extrabold" x-text="'Rs. ' + (viewData?.grand_total || 0).toLocaleString()"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">PAID</span>
                            <span class="text-base font-extrabold text-emerald-400" x-text="'Rs. ' + (viewData?.total_paid || 0).toLocaleString()"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">BALANCE DUE</span>
                            <span class="text-base font-extrabold text-amber-400" x-text="'Rs. ' + (viewData?.total_due || 0).toLocaleString()"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-3 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <button type="button" @click="closeViewModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition cursor-pointer">
                    Close
                </button>
                <div class="flex items-center gap-2">
                    <a :href="viewData?.invoice_url" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.656"/></svg>
                        Print Invoice
                    </a>
                    <a :href="viewData?.edit_url" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        Edit Order
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function ordersIndexManager() {
    return {
        viewModalOpen: false,
        viewData: null,
        openViewModal(data) {
            this.viewData = data;
            this.viewModalOpen = true;
        },
        closeViewModal() {
            this.viewModalOpen = false;
            this.viewData = null;
        }
    };
}

function statusCell(initial, url) {
    return {
        url: url,
        status: initial,
        busy: false,
        open: false,
        panelX: 0,
        panelY: 0,
        options: [
            { key: 'pending',     label: 'Pending',      dot: 'bg-amber-400' },
            { key: 'cutting',     label: 'In Cutting',   dot: 'bg-purple-500' },
            { key: 'stitching',   label: 'In Stitching', dot: 'bg-teal-500' },
            { key: 'in_progress', label: 'In Progress',  dot: 'bg-blue-400' },
            { key: 'completed',   label: 'Completed',    dot: 'bg-emerald-400' },
            { key: 'delivered',   label: 'Delivered',    dot: 'bg-indigo-400' },
            { key: 'cancelled',   label: 'Cancelled',    dot: 'bg-red-400' },
        ],
        get label() {
            return this.options.find(o => o.key === this.status)?.label || this.status;
        },
        get cls() {
            const m = {
                pending:     'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
                cutting:     'bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100',
                stitching:   'bg-teal-50 text-teal-700 border-teal-200 hover:bg-teal-100',
                in_progress: 'bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-100',
                completed:   'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
                delivered:   'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100',
                cancelled:   'bg-red-50 text-red-700 border-red-200 hover:bg-red-100',
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
@endsection
