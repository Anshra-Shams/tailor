@extends('layouts.app')

@section('title', 'Assign Order - Select Orders')

@section('content')
<div class="space-y-6" x-data="orderSelector()">

    {{-- Main Form for Step 1 -> Step 2 (Cutting) or Step 3 (Stitching) submission --}}
    <form id="select-orders-form" method="POST" :action="nextStepAction" @submit="validateSubmission($event)">
        @csrf

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-indigo-100 text-indigo-700 text-xs font-bold uppercase tracking-wider">Step 1 of 3</span>
                    <h2 class="text-2xl font-bold text-slate-800">Select Orders to Assign</h2>
                </div>
                <p class="text-slate-500 mt-1">Select orders using checkboxes and click Next to assign Cutting (Step 2) or Stitching (Step 3) tailors.</p>
            </div>

            {{-- Top Next Button --}}
            <div class="flex items-center gap-3 shrink-0">
                <button type="submit"
                    :class="areAllSelectedInCutting ? 'from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-teal-500/25 hover:shadow-teal-500/35' : 'from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-indigo-500/25 hover:shadow-indigo-500/35'"
                    class="group inline-flex items-center gap-2.5 px-5 py-2.5 bg-gradient-to-r text-white font-bold text-sm rounded-xl shadow-md transition-all duration-200 transform hover:scale-[1.02] active:scale-95 whitespace-nowrap shrink-0">
                    <span x-text="nextStepLabel"></span>
                    <span x-show="selectedOrders.length > 0" x-cloak x-text="selectedOrders.length"
                        :class="areAllSelectedInCutting ? 'text-teal-700' : 'text-indigo-700'"
                        class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-white text-xs font-black shadow-xs"></span>
                    <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Stats Overview --}}
        <div class="grid grid-cols-4 gap-2 sm:gap-3 mb-6">
            <div class="bg-white px-2.5 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-1.125 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-lg font-bold text-slate-800 leading-none block">{{ $stats['total'] }}</span>
                    <p class="text-[11px] font-medium text-slate-500 truncate leading-tight mt-0.5">Total Orders</p>
                </div>
            </div>

            <div class="bg-white px-2.5 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-lg font-bold text-amber-600 leading-none block">{{ $stats['unassigned'] }}</span>
                    <p class="text-[11px] font-medium text-slate-500 truncate leading-tight mt-0.5">Fully Unassigned</p>
                </div>
            </div>

            <div class="bg-white px-2.5 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-lg font-bold text-emerald-600 leading-none block">{{ $stats['assigned'] }}</span>
                    <p class="text-[11px] font-medium text-slate-500 truncate leading-tight mt-0.5">Fully Assigned</p>
                </div>
            </div>

            <div class="bg-white px-2.5 py-3 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <span class="text-lg font-bold text-purple-600 leading-none block">{{ $stats['employees'] }}</span>
                    <p class="text-[11px] font-medium text-slate-500 truncate leading-tight mt-0.5">Active Workers</p>
                </div>
            </div>
        </div>

        {{-- Filter & Search Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                {{-- Search Input --}}
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order #, customer, worker..."
                        class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                {{-- Assignment Filter --}}
                <div class="sm:col-span-3">
                    <select name="assignment" class="block w-full py-2 px-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-slate-700">
                        <option value="">All Assignment States</option>
                        <option value="unassigned" {{ request('assignment') === 'unassigned' ? 'selected' : '' }}>Unassigned Only</option>
                        <option value="partial" {{ request('assignment') === 'partial' ? 'selected' : '' }}>Partially Assigned</option>
                        <option value="assigned" {{ request('assignment') === 'assigned' ? 'selected' : '' }}>Fully Assigned</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="sm:col-span-2">
                    <select name="status" class="block w-full py-2 px-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-slate-700">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cutting" {{ request('status') === 'cutting' ? 'selected' : '' }}>In Cutting</option>
                        <option value="stitching" {{ request('status') === 'stitching' ? 'selected' : '' }}>In Stitching</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                {{-- Filter Action Buttons --}}
                <div class="sm:col-span-2 flex items-center gap-2">
                    <button type="button" @click="applyFilters()" class="flex-1 py-2 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'assignment', 'status']))
                        <a href="{{ route('assign-orders.index') }}" title="Reset filters" class="p-2 border border-slate-200 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            @if ($orders->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-left">
                                <th class="pl-4 pr-2 py-3.5 w-12 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected()" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer w-4 h-4">
                                </th>
                                <th class="px-3 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">Order #</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">Customer &amp; Member</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">Service</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide whitespace-nowrap">Due Date</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">Status</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">Cutting Worker</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide">Stitching Worker</th>
                                <th class="px-4 py-3.5 font-semibold text-slate-500 text-xs uppercase tracking-wide text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($orders as $o)
                                @php
                                    $overdue = $o->status === 'pending' && $o->due_date && $o->due_date->isPast();
                                    $cutter = $o->cuttingEmployee;
                                    $stitcher = $o->stitchingEmployee;
                                @endphp
                                <tr class="hover:bg-indigo-50/40 transition-colors cursor-pointer" @click="toggleOrder({{ $o->id }})">
                                    <td class="pl-4 pr-2 py-4 text-center" @click.stop>
                                        <input type="checkbox" name="order_ids[]" value="{{ $o->id }}" x-model="selectedOrders" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer w-4 h-4">
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-800">#{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        <div class="text-[11px] text-slate-400 mt-0.5">{{ ($o->order_date ?? $o->created_at)?->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-4 min-w-[160px]">
                                        <div class="font-semibold text-slate-800">{{ $o->customer?->name ?? '—' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            @if ($o->member)
                                                {{ $o->member->name }}@if($o->member->relation) <span class="capitalize">({{ $o->member->relation }})</span>@endif
                                            @else
                                                Self
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-slate-800 font-medium">{{ $o->service?->name ?? '—' }}</div>
                                        <div class="text-xs text-slate-400 mt-0.5">Qty: {{ $o->quantity }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($o->due_date)
                                            <span class="@if($overdue) text-red-600 font-semibold @else text-slate-600 @endif">{{ $o->due_date->format('d M Y') }}</span>
                                            @if ($overdue)
                                                <span class="ml-1 text-[10px] uppercase font-bold text-red-500 bg-red-50 border border-red-100 rounded px-1 py-0.5">Overdue</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap" @click.stop x-data="statusCell('{{ $o->status }}', '{{ route('orders.updateStatus', $o) }}')">
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
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($cutter)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-medium">
                                                <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.25 9.75L16.5 12l-2.25 2.25m-4.5 0L7.5 12l2.25-2.25M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/></svg>
                                                {{ $cutter->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($stitcher)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 border border-teal-200 text-teal-700 text-xs font-medium">
                                                <svg class="w-3.5 h-3.5 text-teal-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
                                                {{ $stitcher->name }}
                                            </span>
                                        @else
                                            <span class="text-xs text-slate-400 italic">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 whitespace-nowrap" @click.stop>
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('assign-orders.cutting', ['order_ids' => [$o->id], 'force_cutting' => 1]) }}" title="Edit Worker Assignment" class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            </a>
                                            <button type="button" title="Delete order"
                                                @click.stop="deleteOrder({{ $o->id }}, '#{{ str_pad($o->id, 4, '0', STR_PAD_LEFT) }}')"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-400 hover:text-red-600 hover:border-red-300 hover:bg-red-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
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
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <p class="text-slate-500 font-medium">No orders match your filter criteria.</p>
                </div>
            @endif
        </div>

        {{-- Bottom Fixed Next Action Bar --}}
        <div class="mt-6 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between gap-4">
            <div class="text-sm font-medium text-slate-600">
                <span x-text="selectedOrders.length" :class="areAllSelectedInCutting ? 'text-teal-600' : 'text-indigo-600'" class="font-bold"></span> order(s) selected for worker assignment.
            </div>

            <button type="submit"
                :class="areAllSelectedInCutting ? 'from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-teal-500/25 hover:shadow-teal-500/35' : 'from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-indigo-500/25 hover:shadow-indigo-500/35'"
                class="group inline-flex items-center gap-2.5 px-6 py-2.5 bg-gradient-to-r text-white font-bold text-sm rounded-xl shadow-md transition transform hover:scale-[1.02] active:scale-95 whitespace-nowrap shrink-0">
                <span x-text="nextStepLabel"></span>
                <span x-show="selectedOrders.length > 0" x-cloak x-text="selectedOrders.length"
                    :class="areAllSelectedInCutting ? 'text-teal-700' : 'text-indigo-700'"
                    class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-white text-xs font-black shadow-xs"></span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>

        {{-- Floating Bottom Bar when orders are selected --}}
        <div x-show="selectedOrders.length > 0" x-cloak
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-10"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-10"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-5 min-w-[320px] max-w-lg">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-white font-black text-sm shadow-inner"
                    :class="areAllSelectedInCutting ? 'bg-teal-600' : 'bg-indigo-600'"
                    x-text="selectedOrders.length"></span>
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">Orders Selected</h4>
                    <p class="text-[11px] text-slate-400" x-text="areAllSelectedInCutting ? 'Click Next to proceed to Step 3 (Stitching)' : 'Click Next to proceed to Step 2 (Cutting)'"></p>
                </div>
            </div>

            <button type="submit"
                :class="areAllSelectedInCutting ? 'from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700' : 'from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700'"
                class="ml-auto group inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r text-white font-bold text-xs rounded-xl shadow-md transition transform hover:scale-105 active:scale-95 whitespace-nowrap shrink-0">
                <span x-text="nextStepShortLabel"></span>
                <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                </svg>
            </button>
        </div>
    </form>

</div>

@php
    $ordersMap = [];
    foreach ($orders as $o) {
        $ordersMap[$o->id] = [
            'status' => (string) $o->status,
            'has_cutting' => !empty($o->cutting_employee_id),
            'has_stitching' => !empty($o->stitching_employee_id),
        ];
    }
@endphp

@push('scripts')
<script>
    function orderSelector() {
        return {
            selectedOrders: [],
            orderIds: @json($orders->pluck('id')->values()),
            ordersData: @json($ordersMap),
            get areAllSelectedInCutting() {
                if (this.selectedOrders.length === 0) return false;
                return this.selectedOrders.every(id => {
                    const o = this.ordersData[id];
                    return o && (o.status === 'cutting' || (o.has_cutting && !o.has_stitching));
                });
            },
            get nextStepAction() {
                return this.areAllSelectedInCutting ? "{{ route('assign-orders.stitching') }}" : "{{ route('assign-orders.cutting') }}";
            },
            get nextStepLabel() {
                return this.areAllSelectedInCutting ? 'Next: Assign Stitching (Step 3)' : 'Next: Assign Cutting (Step 2)';
            },
            get nextStepShortLabel() {
                return this.areAllSelectedInCutting ? 'Next: Stitching' : 'Next: Cutting';
            },
            toggleOrder(id) {
                const numId = Number(id);
                const idx = this.selectedOrders.map(Number).indexOf(numId);
                if (idx > -1) {
                    this.selectedOrders.splice(idx, 1);
                } else {
                    this.selectedOrders.push(numId);
                }
            },
            toggleSelectAll(e) {
                if (e.target.checked) {
                    this.selectedOrders = [...this.orderIds];
                } else {
                    this.selectedOrders = [];
                }
            },
            isAllSelected() {
                return this.orderIds.length > 0 && this.selectedOrders.length === this.orderIds.length;
            },
            deleteOrder(id, name) {
                const doDelete = () => {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "{{ url('orders') }}/" + id;
                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]').content;
                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(token);
                    form.appendChild(method);
                    document.body.appendChild(form);
                    form.submit();
                };
                if (window.Swal) {
                    Swal.fire({
                        title: 'Are you sure?',
                        html: `"<strong>${name}</strong>" will be permanently deleted.<br>This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        focusCancel: true,
                    }).then((result) => {
                        if (result.isConfirmed) doDelete();
                    });
                } else if (confirm(`Delete ${name}?`)) {
                    doDelete();
                }
            },
            applyFilters() {
                const form = document.getElementById('select-orders-form');
                form.method = 'GET';
                form.action = "{{ route('assign-orders.index') }}";
                form.submit();
            },
            validateSubmission(e) {
                // If form is submitting GET for filters, don't validate selection
                if (e.submitter && e.submitter.getAttribute('type') === 'button') {
                    return;
                }
                if (this.selectedOrders.length === 0) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'No Orders Selected',
                            text: 'Pehle kam az kam ek order select karein (Please select at least one order to proceed).',
                            icon: 'warning',
                            confirmButtonColor: '#4f46e5'
                        });
                    } else {
                        alert('Pehle kam az kam ek order select karein (Please select at least one order to proceed).');
                    }
                }
            }
        }
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
                    pending:     'bg-amber-50 text-amber-700 border-amber-200',
                    cutting:     'bg-purple-50 text-purple-700 border-purple-200',
                    stitching:   'bg-teal-50 text-teal-700 border-teal-200',
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
@endsection
