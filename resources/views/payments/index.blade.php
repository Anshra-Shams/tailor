@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="space-y-6" x-data="paymentsPage()">

    {{-- Header + Stats --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Payments</h2>
            <p class="text-slate-500 mt-1">Receive payments for pending and partially paid orders</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-center min-w-[110px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-slate-400">Due Orders</p>
                <p class="text-lg font-bold text-slate-800" x-text="allOrders.length"></p>
            </div>
            <div class="bg-white border border-red-100 rounded-xl px-4 py-2.5 text-center min-w-[130px]">
                <p class="text-[11px] uppercase font-semibold tracking-wide text-red-400">Outstanding</p>
                <p class="text-lg font-bold text-red-600" x-text="'Rs. ' + money({{ (int) $outstandingDue }})"></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- LEFT: Search dropdown + Recent payments --}}
        <div class="lg:col-span-7 space-y-4">

            {{-- Search dropdown --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-4 relative" @click.outside="dropdownOpen = false">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <input type="text" x-model="searchQuery"
                        @input.debounce.300ms="filterDropdown()"
                        @focus="openDropdown()"
                        placeholder="Search by Order ID, customer name, phone, or member..."
                        class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                {{-- Dropdown --}}
                <div x-show="dropdownOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1"
                    class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl z-30 max-h-[420px] overflow-y-auto">

                    <div x-show="dropdownLoading" class="px-5 py-8 text-center text-xs text-slate-400">Loading orders...</div>
                    <div x-show="!dropdownLoading && dropdownOrders.length === 0" class="px-5 py-8 text-center text-xs text-slate-400">No due orders found.</div>

                    <div class="divide-y divide-slate-50" x-show="dropdownOrders.length > 0">
                        <template x-for="o in dropdownOrders" :key="o.id">
                            <button type="button" @click="select(o); dropdownOpen = false;"
                                class="w-full text-left px-5 py-3 hover:bg-indigo-50/60 transition-colors flex items-center gap-3"
                                :class="selected && selected.id === o.id ? 'bg-indigo-50' : ''">
                                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0" x-text="o.no"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-800 truncate" x-text="o.customer"></span>
                                        <span class="text-[11px] text-slate-400 truncate" x-text="memberLabel(o)"></span>
                                    </span>
                                    <span class="block text-[11px] text-slate-500 mt-0.5 truncate">
                                        <span x-text="o.service"></span> &times; <span x-text="o.quantity"></span>
                                        &middot; due <span x-text="o.due_date"></span>
                                    </span>
                                </span>
                                <span class="text-right flex-shrink-0">
                                    <span class="block text-sm font-bold text-red-600" x-text="'Rs. ' + money(o.remaining)"></span>
                                    <span class="block text-[10px] uppercase font-semibold tracking-wide mt-0.5"
                                        :class="badgeCls(o.payment_status)" x-text="o.payment_status"></span>
                                </span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Recent Payments Log --}}
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mt-4">
                <button type="button" @click="showRecent = !showRecent; if (showRecent && recentPayments.length === 0) loadRecent()"
                    class="w-full flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-bold text-slate-700">Recent Payments</span>
                        <span class="text-xs text-slate-400" x-text="recentPayments.length ? '(' + recentPayments.length + ')' : ''"></span>
                    </span>
                    <svg class="w-4 h-4 text-slate-400 transition-transform" :class="showRecent ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </button>
                <div x-show="showRecent" x-transition class="border-t border-slate-100">
                    <div x-show="loadingRecent" class="px-5 py-6 text-center text-xs text-slate-400">Loading recent payments...</div>
                    <div x-show="!loadingRecent && recentPayments.length === 0" class="px-5 py-6 text-center text-xs text-slate-400">No payments recorded yet.</div>
                    <div class="divide-y divide-slate-50 max-h-80 overflow-y-auto" x-show="recentPayments.length > 0">
                        <template x-for="p in recentPayments" :key="p.id">
                            <div class="px-5 py-3 flex items-center gap-3 hover:bg-indigo-50/40 transition-colors">
                                <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-600 text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0" x-text="'#' + p.order_no"></span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-slate-800 truncate" x-text="p.customer"></span>
                                    <span class="block text-[11px] text-slate-400 truncate">
                                        <span x-text="p.method"></span>
                                        <span x-show="p.notes"> · <span x-text="p.notes"></span></span>
                                    </span>
                                </span>
                                <span class="text-right flex-shrink-0">
                                    <span class="block text-sm font-bold text-emerald-700" x-text="'Rs. ' + money(p.amount)"></span>
                                    <span class="block text-[10px] text-slate-400" x-text="p.date + ', ' + p.time"></span>
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Payment panel --}}
        <div class="lg:col-span-5 lg:sticky lg:top-0">

            {{-- Empty state --}}
            <div x-show="!selected" class="bg-white rounded-2xl border border-dashed border-slate-300 px-6 py-16 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-indigo-50 flex items-center justify-center">
                    <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                </div>
                <h3 class="text-base font-semibold text-slate-700">No order selected</h3>
                <p class="text-sm text-slate-400 mt-1">Click the search bar and pick an order to record a payment.</p>
            </div>

            {{-- Selected panel --}}
            <div x-show="selected" x-transition class="space-y-4">
                <form @submit.prevent="save()" class="space-y-4">
                    @csrf
                    <input type="hidden" name="order_id" :value="selected?.id">

                    {{-- Selected order card --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[11px] uppercase font-bold tracking-wide text-slate-400">Selected Order</p>
                                <p class="text-lg font-bold text-slate-800 mt-0.5">#<span x-text="selected?.no"></span> · <span x-text="selected?.customer"></span></p>
                                <p class="text-sm text-slate-500 mt-0.5">
                                    <span x-text="memberLabel(selected || {})"></span>
                                    <span class="mx-1">&middot;</span>
                                    <span x-text="selected ? (selected.service + ' × ' + selected.quantity) : ''"></span>
                                </p>
                            </div>
                            <button type="button" @click="deselect()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Summary --}}
                        <div class="grid grid-cols-3 gap-2 mt-4">
                            <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5 text-center">
                                <p class="text-[10px] uppercase font-semibold tracking-wide text-slate-400">Total</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5" x-text="'Rs. ' + money(selected?.total)"></p>
                            </div>
                            <div class="rounded-xl bg-emerald-50/70 border border-emerald-100 px-3 py-2.5 text-center">
                                <p class="text-[10px] uppercase font-semibold tracking-wide text-emerald-500">Paid</p>
                                <p class="text-sm font-bold text-emerald-700 mt-0.5" x-text="'Rs. ' + money(selected?.paid)"></p>
                            </div>
                            <div class="rounded-xl bg-red-50/80 border border-red-100 px-3 py-2.5 text-center">
                                <p class="text-[10px] uppercase font-semibold tracking-wide text-red-400">Remaining</p>
                                <p class="text-sm font-bold text-red-600 mt-0.5" x-text="'Rs. ' + money(selected?.remaining)"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Payment entry --}}
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Record Payment</h3>

                        <div>
                            <label for="amount" class="block text-xs font-medium text-slate-500 mb-1">Amount (Rs.) <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" x-model="pay.amount"
                                   class="w-full py-2.5 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500"
                                   :class="pay.amount !== '' && invalidAmount ? 'border-red-400 bg-red-50' : 'border-slate-300'">
                            <div class="mt-2 flex items-center justify-between gap-2">
                                @error('amount')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                                <button type="button" @click="payFull()" class="ml-auto inline-flex items-center px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition">Pay Full Due</button>
                            </div>
                            <p x-show="error" x-text="error" class="mt-1 text-xs text-red-500"></p>
                        </div>

                        <div>
                            <label for="method" class="block text-xs font-medium text-slate-500 mb-1">Payment Method <span class="text-red-500">*</span></label>
                            <select name="method" id="method" x-model="pay.method" class="w-full py-2.5 px-3 rounded-lg border border-slate-300 text-sm capitalize focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online Payment</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-medium text-slate-500 mb-1">Note (optional)</label>
                            <input type="text" name="notes" id="notes" x-model="pay.notes" maxlength="500" placeholder="e.g. receipt no, reference"
                                   class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Live remaining calc --}}
                        <div class="rounded-xl bg-indigo-50 border border-indigo-100 px-4 py-3 flex items-center justify-between text-sm">
                            <span class="text-indigo-700">After this payment:</span>
                            <span class="font-bold" :class="remainingAfter() === 0 ? 'text-emerald-600' : 'text-indigo-900'" x-text="'Rs. ' + money(remainingAfter()) + (remainingAfter() === 0 ? ' (Fully Paid)' : ' left')"></span>
                        </div>

                        <button type="submit" :disabled="invalidAmount || saving"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed rounded-xl shadow-sm transition">
                            <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            <span x-text="saving ? 'Saving...' : 'Save Payment'"></span>
                        </button>
                    </div>
                </form>

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
        allOrders: @json($initialOrders),
        dropdownOrders: @json($initialOrders),
        searchQuery: '',
        dropdownOpen: false,
        dropdownLoading: false,
        selected: null,
        saving: false,
        error: '',
        pay: { amount: '', method: 'cash', notes: '' },
        showRecent: false,
        recentPayments: [],
        loadingRecent: false,

        async init() {
            this.$watch('pay.amount', () => { this.error = ''; });
            // If browser restores this page from cache (back button), reload for fresh balances
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) location.reload();
            });
            this.loadRecent();
        },

        updateInLists(u) {
            const swap = (arr) => {
                const i = arr.findIndex(x => x.id === u.id);
                if (i !== -1) u.payment_status === 'paid' ? arr.splice(i, 1) : arr.splice(i, 1, u);
            };
            swap(this.dropdownOrders);
            swap(this.allOrders);
        },

        async save() {
            this.error = '';
            if (!this.selected) return;
            if (this.invalidAmount()) {
                this.error = this.invalidMsg();
                return;
            }

            // Freshness guard: re-check balance right before submitting
            try {
                const r0 = await fetch('{{ route("api.payments.search") }}?q=' + this.selected.id, { headers: { 'Accept': 'application/json' } });
                const l0 = await r0.json();
                const f0 = Array.isArray(l0) ? l0.find(x => x.id === this.selected.id) : null;
                if (!f0) {
                    Swal.fire({ icon: 'info', title: 'Already settled', text: 'This order is fully paid. Refreshing...', timer: 1800, showConfirmButton: false });
                    await this.doSearchRefresh();
                    return;
                }
                if (Math.abs(f0.remaining - this.selected.remaining) > 0.009) {
                    this.updateInLists(f0);
                    this.selected = f0;
                    this.error = 'Balance was outdated — remaining is now Rs. ' + this.money(f0.remaining) + '. Amount set to full due, press Save again.';
                    this.payFull();
                    return;
                }
            } catch (e) {}

            this.saving = true;
            try {
                const fd = new FormData();
                fd.append('order_id', this.selected.id);
                fd.append('amount', this.pay.amount);
                fd.append('method', this.pay.method);
                fd.append('notes', this.pay.notes);

                const res = await fetch('{{ route("payments.store") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: fd,
                });
                let d = null;
                try { d = await res.json(); } catch (e) {}

                if (res.ok && d && d.success) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2600, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: d.message });

                    const upd = d.order;
                    if (upd.payment_status === 'paid') {
                        this.updateInLists(upd);
                        this.selected = null;
                    } else {
                        this.updateInLists(upd);
                        this.selected = upd;
                    }
                    this.pay = { amount: '', method: 'cash', notes: '' };
                    if (this.showRecent) this.loadRecent();
                } else {
                    this.error = (d && d.message) ? d.message : 'Could not save payment. Please try again.';
                }
            } catch (e) {
                this.error = 'Network error. Please try again.';
            }
            this.saving = false;
        },

        money(n) {
            return (Number(n) || 0).toLocaleString('en-PK');
        },

        memberLabel(o) {
            if (!o) return '';
            return o.member ? (o.member + (o.relation ? ' (' + o.relation + ')' : '')) : 'Self';
        },

        badgeCls(s) {
            return s === 'paid'
                ? 'text-emerald-600'
                : (s === 'partial' ? 'text-amber-600' : 'text-red-500');
        },

        async openDropdown() {
            this.dropdownOpen = true;
            if (this.searchQuery.trim() === '' && this.dropdownOrders.length === 0) {
                this.dropdownLoading = true;
                try {
                    const r = await fetch('{{ route("api.payments.search") }}?q=', { headers: { 'Accept': 'application/json' } });
                    this.dropdownOrders = await r.json();
                    this.allOrders = this.dropdownOrders;
                } catch (e) {}
                this.dropdownLoading = false;
            }
        },

        async filterDropdown() {
            this.dropdownOpen = true;
            this.dropdownLoading = true;
            try {
                const r = await fetch('{{ route("api.payments.search") }}?q=' + encodeURIComponent(this.searchQuery.trim()), { headers: { 'Accept': 'application/json' } });
                this.dropdownOrders = await r.json();
                if (this.searchQuery.trim() === '') this.allOrders = this.dropdownOrders;
            } catch (e) {}
            this.dropdownLoading = false;
        },

        async select(o) {
            this.history = [];
            this.pay = { amount: '', method: 'cash', notes: '' };
            this.error = '';
            // Always fetch fresh balance from server so Pay Full Due is never stale
            let fresh = o;
            try {
                const r = await fetch('{{ route("api.payments.search") }}?q=' + o.id, { headers: { 'Accept': 'application/json' } });
                const list = await r.json();
                const found = Array.isArray(list) ? list.find(x => x.id === o.id) : null;
                if (!found) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Already settled',
                        text: 'This order has been fully paid. The list is being refreshed.',
                        timer: 1800,
                        showConfirmButton: false,
                    });
                    await this.doSearchRefresh();
                    return;
                }
                fresh = found;
                // keep the list row in sync too
                const idx = this.dropdownOrders.findIndex(x => x.id === o.id);
                if (idx !== -1) this.dropdownOrders.splice(idx, 1, fresh);
                const aidx = this.allOrders.findIndex(x => x.id === o.id);
                if (aidx !== -1) this.allOrders.splice(aidx, 1, fresh);
            } catch (e) {}
            this.selected = fresh;
            if (window.innerWidth < 1024) {
                document.querySelector('[x-show="selected"]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        },

        async doSearchRefresh() {
            try {
                const r = await fetch('{{ route("api.payments.search") }}?q=' + encodeURIComponent(this.searchQuery.trim()), { headers: { 'Accept': 'application/json' } });
                this.dropdownOrders = await r.json();
                this.allOrders = this.dropdownOrders;
                this.selected = null;
            } catch (e) {}
        },

        async loadRecent() {
            this.loadingRecent = true;
            try {
                const r = await fetch('{{ route("api.payments.recent") }}', { headers: { 'Accept': 'application/json' } });
                this.recentPayments = await r.json();
            } catch (e) {
                this.recentPayments = [];
            }
            this.loadingRecent = false;
        },

        deselect() {
            this.selected = null;
        },

        payFull() {
            if (this.selected) this.pay.amount = String(this.selected.remaining);
        },

        amountNum() {
            return parseFloat(this.pay.amount) || 0;
        },

        invalidAmount() {
            if (!this.selected) return true;
            const a = this.amountNum();
            return !(a > 0) || a > this.selected.remaining + 0.009;
        },

        invalidMsg() {
            if (!this.selected) return 'Select an order first';
            const a = this.amountNum();
            if (!(a > 0)) return 'Enter a payment amount greater than zero';
            return 'Payment cannot exceed the remaining due of Rs. ' + this.money(this.selected.remaining);
        },

        remainingAfter() {
            if (!this.selected) return 0;
            return Math.max(0, Math.round((this.selected.remaining - Math.max(0, this.amountNum())) * 100) / 100);
        },
    };
}
</script>
@endpush