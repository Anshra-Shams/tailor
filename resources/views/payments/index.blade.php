@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="space-y-6" x-data="paymentsPage()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Payments</h2>
            <p class="text-slate-500 mt-1">View all payments and record new ones</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Stats --}}
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-center min-w-[110px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Due Orders</p>
                <p class="text-lg font-bold text-slate-800">{{ $dueCount }}</p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl px-4 py-2.5 text-center min-w-[130px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-red-400">Outstanding</p>
                <p class="text-lg font-bold text-red-600">Rs. {{ number_format($outstandingDue) }}</p>
            </div>
            {{-- Add Payment Button --}}
            <button type="button" @click="openModal()"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Payment
            </button>
        </div>
    </div>

    {{-- Payments Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Table toolbar --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-700">All Payments</h3>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input type="text" x-model="tableSearch" @input.debounce.300ms="filterTable()"
                        placeholder="Search payments..."
                        autocomplete="off"
                        spellcheck="false"
                        class="pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 w-56">
                </div>
                <button @click="loadPayments()" title="Refresh"
                    class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" :class="tableLoading ? 'animate-spin' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Loading state --}}
        <div x-show="tableLoading" class="px-5 py-12 text-center">
            <svg class="w-6 h-6 text-indigo-400 animate-spin mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-xs text-slate-400 mt-2">Loading payments...</p>
        </div>

        {{-- Empty state --}}
        <div x-show="!tableLoading && filteredPayments.length === 0" class="px-5 py-14 text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-slate-600">No payments found</h4>
            <p class="text-xs text-slate-400 mt-1">Click "Add Payment" to record the first payment.</p>
        </div>

        {{-- Table --}}
        <div x-show="!tableLoading && filteredPayments.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Order</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Customer</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 hidden sm:table-cell">Member</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Amount</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 hidden md:table-cell">Method</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 hidden lg:table-cell">Type</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <template x-for="p in filteredPayments" :key="p.id">
                        <tr class="hover:bg-indigo-50/30 transition-colors">
                            {{-- Order # --}}
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-[10px]" x-text="'#' + p.order_no"></span>
                            </td>
                            {{-- Customer --}}
                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-slate-800" x-text="p.customer"></span>
                                <span class="block text-[11px] text-slate-400" x-text="p.phone"></span>
                            </td>
                            {{-- Member --}}
                            <td class="px-4 py-3.5 hidden sm:table-cell">
                                <span class="text-slate-600 text-[12px]" x-text="p.member || '—'"></span>
                            </td>
                            {{-- Amount --}}
                            <td class="px-4 py-3.5 text-right">
                                <span class="font-bold text-emerald-700" x-text="'Rs. ' + money(p.amount)"></span>
                            </td>
                            {{-- Method --}}
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold capitalize"
                                    :class="{
                                        'bg-emerald-50 text-emerald-700': p.method === 'cash',
                                        'bg-blue-50 text-blue-700': p.method === 'bank transfer',
                                        'bg-purple-50 text-purple-700': p.method === 'online payment',
                                        'bg-slate-100 text-slate-600': p.method === 'other',
                                    }"
                                    x-text="p.method"></span>
                            </td>
                            {{-- Type --}}
                            <td class="px-4 py-3.5 hidden lg:table-cell">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                    :class="p.type === 'advance' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600'"
                                    x-text="p.type === 'advance' ? 'Advance' : 'Payment'"></span>
                            </td>
                            {{-- Date --}}
                            <td class="px-5 py-3.5">
                                <span class="block text-slate-700 text-[12px] font-medium" x-text="p.date"></span>
                                <span class="block text-slate-400 text-[11px]" x-text="p.time"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Table footer --}}
        <div x-show="!tableLoading && filteredPayments.length > 0" class="px-5 py-3 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-400" x-text="filteredPayments.length + ' payment(s) shown'"></p>
            <button x-show="hasMore" @click="loadMore()" :disabled="loadingMore"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold disabled:opacity-50 transition">
                <span x-text="loadingMore ? 'Loading...' : 'Load more'"></span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         ADD PAYMENT MODAL
    ═══════════════════════════════════════════════════════════ --}}
    <div x-show="modalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeModal()"></div>

        {{-- Modal panel --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Add Payment</h3>
                </div>
                <button type="button" @click="closeModal()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="px-6 py-5 space-y-5">

                {{-- Step 1: Select Order --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Select Order <span class="text-red-500">*</span>
                    </label>
                    <div class="relative" @click.outside="orderDropOpen = false">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <input type="text"
                                x-model="orderSearch"
                                @input.debounce.300ms="searchOrders()"
                                @focus="orderDropOpen = true; if (!orderSearch) searchOrders()"
                                placeholder="Search by name, order#, phone..."
                                autocomplete="off"
                                spellcheck="false"
                                class="w-full pl-9 pr-10 py-2.5 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                :class="selected ? 'border-emerald-400 bg-emerald-50/30' : ''">
                            <button x-show="selected" x-cloak @click="clearOrder()" type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            {{-- Loading spinner inside input --}}
                            <div x-show="orderDropLoading" x-cloak
                                class="absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="w-4 h-4 text-indigo-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Order dropdown --}}
                        <div x-show="orderDropOpen && !selected"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-50 max-h-60 overflow-y-auto">
                            <div x-show="orderDropLoading" class="px-4 py-5 text-center text-xs text-slate-400">Searching orders...</div>
                            <div x-show="!orderDropLoading && orderDropList.length === 0" class="px-4 py-5 text-center text-xs text-slate-400">No due orders found.</div>
                            <div class="divide-y divide-slate-50" x-show="orderDropList.length > 0">
                                <template x-for="o in orderDropList" :key="o.id">
                                    <button type="button" @click="selectOrder(o)"
                                        class="w-full text-left px-4 py-3 hover:bg-indigo-50/60 transition-colors flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0" x-text="'#'+o.no"></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-semibold text-slate-800 text-sm truncate" x-text="o.customer"></span>
                                            <span class="block text-[11px] text-slate-500 truncate" x-text="o.service + ' × ' + o.quantity + (o.member ? ' · ' + o.member : '')"></span>
                                        </span>
                                        <span class="text-right flex-shrink-0">
                                            <span class="block text-sm font-bold text-red-600" x-text="'Rs. ' + money(o.remaining)"></span>
                                            <span class="block text-[10px] uppercase font-semibold tracking-wide mt-0.5"
                                                :class="o.payment_status === 'partial' ? 'text-amber-600' : 'text-red-500'"
                                                x-text="o.payment_status"></span>
                                        </span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Selected order summary --}}
                    <div x-show="selected" x-transition class="mt-3 rounded-xl bg-indigo-50 border border-indigo-100 p-3">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0" x-text="'#' + (selected?.no || '')"></span>
                            <div class="min-w-0 flex-1">
                                <p class="font-bold text-slate-800 text-sm" x-text="selected?.customer"></p>
                                <p class="text-[11px] text-slate-500" x-text="(selected?.member || 'Self') + ' · ' + (selected?.service || '') + ' × ' + (selected?.quantity || '')"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3">
                            <div class="rounded-lg bg-white border border-slate-200 px-2 py-1.5 text-center">
                                <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">Total</p>
                                <p class="text-xs font-bold text-slate-800 mt-0.5" x-text="'Rs. ' + money(selected?.total)"></p>
                            </div>
                            <div class="rounded-lg bg-emerald-50 border border-emerald-100 px-2 py-1.5 text-center">
                                <p class="text-[10px] text-emerald-600 font-semibold uppercase tracking-wide">Paid</p>
                                <p class="text-xs font-bold text-emerald-700 mt-0.5" x-text="'Rs. ' + money(selected?.paid)"></p>
                            </div>
                            <div class="rounded-lg bg-red-50 border border-red-100 px-2 py-1.5 text-center">
                                <p class="text-[10px] text-red-400 font-semibold uppercase tracking-wide">Due</p>
                                <p class="text-xs font-bold text-red-600 mt-0.5" x-text="'Rs. ' + money(selected?.remaining)"></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Account / Amount / Pay Full — single row --}}
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    {{-- Account (payment method) --}}
                    <div>
                        <label for="modal_account" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                            Account <span class="text-red-500">*</span>
                        </label>
                        <select id="modal_account" x-model="pay.account_id"
                            class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                            <option value="">Select account</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Amount --}}
                    <div>
                        <label for="modal_amount" class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                            Amount (Rs.) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="modal_amount" step="0.01" min="0.01" x-model="pay.amount"
                            class="w-full py-2.5 px-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            :class="pay.amount !== '' && modalInvalidAmount() ? 'border-red-400 bg-red-50' : 'border-slate-300'"
                            placeholder="0">
                    </div>

                    {{-- Pay full due --}}
                    <button type="button" @click="payFull()"
                        :disabled="!selected"
                        class="px-3 py-2.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 disabled:opacity-40 disabled:cursor-not-allowed transition whitespace-nowrap">
                        Pay Full Due
                    </button>
                </div>
                <p x-show="pay.amount !== '' && modalInvalidAmount()" x-text="modalInvalidMsg()" class="text-xs text-red-500"></p>

                {{-- Live balance preview --}}
                <div x-show="selected" class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 flex items-center justify-between text-sm">
                    <span class="text-indigo-700 font-medium">After this payment:</span>
                    <span class="font-bold"
                        :class="modalRemainingAfter() === 0 ? 'text-emerald-600' : 'text-indigo-900'"
                        x-text="'Rs. ' + money(modalRemainingAfter()) + (modalRemainingAfter() === 0 ? ' (Fully Paid ✓)' : ' left')">
                    </span>
                </div>

                {{-- Error --}}
                <p x-show="modalError" x-text="modalError" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"></p>
            </div>

            {{-- Modal footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="closeModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Cancel
                </button>
                <button type="button" @click="savePayment()"
                    :disabled="!selected || modalInvalidAmount() || saving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed rounded-xl shadow-sm transition">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <svg x-show="saving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="saving ? 'Saving...' : 'Save Payment'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]').content;

function paymentsPage() {
    return {
        // Table
        allPayments: [],
        filteredPayments: [],
        tableSearch: '',
        tableLoading: false,
        loadingMore: false,
        hasMore: false,
        page: 1,

        // Modal
        modalOpen: false,
        orderSearch: '',
        orderDropOpen: false,
        orderDropLoading: false,
        orderDropList: [],
        selected: null,
        pay: { amount: '', account_id: '' },
        saving: false,
        modalError: '',

        async init() {
            await this.loadPayments();

            // Reload fresh data on back-button
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) location.reload();
            });
        },

        // ────────────────────────────────
        // TABLE
        // ────────────────────────────────
        async loadPayments() {
            this.tableLoading = true;
            this.page = 1;
            try {
                const r = await fetch('{{ route("api.payments.recent") }}?limit=50', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.allPayments = data;
                this.hasMore = data.length === 50;
                this.filterTable();
            } catch (e) {
                this.allPayments = [];
                this.filteredPayments = [];
            }
            this.tableLoading = false;
        },

        filterTable() {
            const q = this.tableSearch.trim().toLowerCase();
            if (!q) {
                this.filteredPayments = [...this.allPayments];
                return;
            }
            this.filteredPayments = this.allPayments.filter(p =>
                (p.customer || '').toLowerCase().includes(q) ||
                (p.phone || '').toLowerCase().includes(q) ||
                String(p.order_no).includes(q) ||
                (p.method || '').toLowerCase().includes(q) ||
                (p.type || '').toLowerCase().includes(q) ||
                (p.notes || '').toLowerCase().includes(q) ||
                (p.member || '').toLowerCase().includes(q)
            );
        },

        async loadMore() {
            this.loadingMore = true;
            this.page++;
            try {
                const r = await fetch(`{{ route("api.payments.recent") }}?limit=50&page=${this.page}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await r.json();
                this.allPayments = [...this.allPayments, ...data];
                this.hasMore = data.length === 50;
                this.filterTable();
            } catch (e) {}
            this.loadingMore = false;
        },

        // ────────────────────────────────
        // MODAL
        // ────────────────────────────────
        openModal() {
            this.modalOpen = true;
            this.clearOrder();
            this.pay = { amount: '', method: 'cash', notes: '' };
            this.modalError = '';
            this.$nextTick(() => {
                this.searchOrders();
            });
        },

        closeModal() {
            this.modalOpen = false;
            this.clearOrder();
            this.pay = { amount: '', method: 'cash', notes: '' };
            this.modalError = '';
        },

        clearOrder() {
            this.selected = null;
            this.orderSearch = '';
            this.orderDropList = [];
            this.orderDropOpen = false;
            this.pay.amount = '';
        },

        async searchOrders() {
            this.orderDropLoading = true;
            try {
                const q = encodeURIComponent(this.orderSearch.trim());
                const r = await fetch(`{{ route("api.payments.search") }}?q=${q}`, {
                    headers: { 'Accept': 'application/json' }
                });
                this.orderDropList = await r.json();
                this.orderDropOpen = true;
            } catch (e) {
                this.orderDropList = [];
            }
            this.orderDropLoading = false;
        },

        async selectOrder(o) {
            // Fetch fresh balance
            let fresh = o;
            try {
                const r = await fetch(`{{ route("api.payments.search") }}?q=${o.id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const list = await r.json();
                const found = Array.isArray(list) ? list.find(x => x.id === o.id) : null;
                if (!found) {
                    Swal.fire({ icon: 'info', title: 'Already settled', text: 'This order is fully paid.', timer: 1800, showConfirmButton: false });
                    this.searchOrders();
                    return;
                }
                fresh = found;
            } catch (e) {}
            this.selected = fresh;
            this.orderSearch = `#${fresh.no} · ${fresh.customer}`;
            this.orderDropOpen = false;
            this.pay.amount = '';
            this.modalError = '';
        },

        async savePayment() {
            this.modalError = '';
            if (!this.selected) { this.modalError = 'Select an order first.'; return; }
            if (!this.pay.account_id) { this.modalError = 'Select an account.'; return; }
            if (this.modalInvalidAmount()) { this.modalError = this.modalInvalidMsg(); return; }

            // Freshness guard
            try {
                const r0 = await fetch(`{{ route("api.payments.search") }}?q=${this.selected.id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const l0 = await r0.json();
                const f0 = Array.isArray(l0) ? l0.find(x => x.id === this.selected.id) : null;
                if (!f0) {
                    Swal.fire({ icon: 'info', title: 'Already settled', text: 'This order is fully paid.', timer: 1800, showConfirmButton: false });
                    this.searchOrders();
                    this.clearOrder();
                    return;
                }
                if (Math.abs(f0.remaining - this.selected.remaining) > 0.009) {
                    this.selected = f0;
                    this.modalError = 'Balance was outdated — remaining is now Rs. ' + this.money(f0.remaining) + '. Amount updated, press Save again.';
                    this.payFull();
                    return;
                }
            } catch (e) {}

            this.saving = true;
            try {
                const fd = new FormData();
                fd.append('order_id', this.selected.id);
                fd.append('amount', this.pay.amount);
                fd.append('account_id', this.pay.account_id);

                const res = await fetch('{{ route("payments.store") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: fd,
                });
                let d = null;
                try { d = await res.json(); } catch (e) {}

                if (res.ok && d && d.success) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2800, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: d.message });
                    this.closeModal();
                    await this.loadPayments(); // Refresh table
                } else {
                    this.modalError = (d && d.message) ? d.message : 'Could not save payment. Please try again.';
                }
            } catch (e) {
                this.modalError = 'Network error. Please try again.';
            }
            this.saving = false;
        },

        payFull() {
            if (this.selected) this.pay.amount = String(this.selected.remaining);
        },

        // ────────────────────────────────
        // HELPERS
        // ────────────────────────────────
        money(n) {
            return (Number(n) || 0).toLocaleString('en-PK');
        },

        amountNum() {
            return parseFloat(this.pay.amount) || 0;
        },

        modalInvalidAmount() {
            if (!this.selected) return true;
            const a = this.amountNum();
            return !(a > 0) || a > this.selected.remaining + 0.009;
        },

        modalInvalidMsg() {
            if (!this.selected) return 'Select an order first';
            const a = this.amountNum();
            if (!(a > 0)) return 'Enter a payment amount greater than zero';
            return 'Payment cannot exceed remaining due of Rs. ' + this.money(this.selected.remaining);
        },

        modalRemainingAfter() {
            if (!this.selected) return 0;
            return Math.max(0, Math.round((this.selected.remaining - Math.max(0, this.amountNum())) * 100) / 100);
        },
    };
}
</script>
@endpush