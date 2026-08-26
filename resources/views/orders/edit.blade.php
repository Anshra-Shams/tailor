@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="max-w-3xl mx-auto space-y-6" x-data="editOrder()">

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('orders.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-indigo-600 hover:border-indigo-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Order</h2>
            <p class="text-slate-500 mt-0.5 text-sm">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} &middot; created {{ $order->created_at->format('d M Y') }}</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700">
            Please fix the errors below and try again.
        </div>
    @endif

    <form method="POST" action="{{ route('orders.update', $order) }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Customer (read only) --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-full bg-indigo-500 text-white font-semibold flex items-center justify-center flex-shrink-0">{{ strtoupper(substr($order->customer?->name ?? '?', 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $order->customer?->name ?? '—' }}</p>
                    <p class="text-sm text-slate-500">{{ $order->customer?->phone }}</p>
                </div>
                <span class="ml-auto text-[11px] uppercase font-semibold tracking-wide text-slate-400 bg-slate-100 rounded-full px-3 py-1">Customer fixed</span>
            </div>

            {{-- Member --}}
            <div class="mt-4">
                <label for="member_id" class="block text-xs font-medium text-slate-500 mb-1">Member</label>
                <select name="member_id" id="member_id" x-model="form.member_id" @change="onMemberChange()" class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('member_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <option value="">Self ({{ $order->customer?->name }})</option>
                    @foreach ($order->customer?->members ?? [] as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}@if($m->relation) ({{ $m->relation }})@endif</option>
                    @endforeach
                </select>
                @error('member_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Order details --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wide">Order Details</h3>
                @php
                    $badgeColors = [
                        'pending'     => 'bg-amber-50 text-amber-700 border-amber-200',
                        'in_progress' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'delivered'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'cancelled'   => 'bg-red-50 text-red-700 border-red-200',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-xs font-semibold capitalize {{ $badgeColors[$order->status] ?? 'bg-slate-50 text-slate-600 border-slate-200' }}">
                    {{ str_replace('_', ' ', $order->status) }}
                    <span class="text-[10px] font-normal text-slate-400 normal-case">(change from table)</span>
                </span>
            </div>

            <div>
                <label for="service_id" class="block text-xs font-medium text-slate-500 mb-1">Service <span class="text-red-500">*</span></label>
                <select name="service_id" id="service_id" required x-model="form.service_id" @change="onServiceChange()" class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('service_id') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    <template x-for="s in availableServices" :key="s.id">
                        <option :value="s.id" x-text="s.name + ' — Rs. ' + money(s.price)"></option>
                    </template>
                </select>
                <p x-show="availableServices.length === 0 && !loadingServices" class="mt-2 text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">No measured services for this member yet.</p>
                @error('service_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                <div class="mt-2 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-[11px] text-slate-400">Only services with saved measurements for the selected member are listed.</p>
                    <a href="{{ route('measurements.create') }}" class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Measurement
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="quantity" class="block text-xs font-medium text-slate-500 mb-1">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="quantity" min="1" step="1" required x-model="form.quantity"
                           class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('quantity') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('quantity')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="price" class="block text-xs font-medium text-slate-500 mb-1">Price / pc (Rs.) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" id="price" min="0" step="0.01" required x-model="form.price"
                           class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('price') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('price')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="paid_amount" class="block text-xs font-medium text-slate-500 mb-1">Paid (Rs.)</label>
                    <input type="number" name="paid_amount" id="paid_amount" min="0" step="0.01" x-model="form.paid_amount"
                           class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('paid_amount') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('paid_amount')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 flex items-center justify-between text-sm">
                <span class="text-slate-500">Line total: <span class="font-semibold text-slate-800" x-text="'Rs. ' + money(lineTotal())"></span></span>
                <span :class="due() > 0 ? 'text-red-600 font-semibold' : 'text-emerald-600 font-semibold'" x-text="'Due: Rs. ' + money(due())"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="due_date" class="block text-xs font-medium text-slate-500 mb-1">Delivery Date <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" id="due_date" required value="{{ old('due_date', optional($order->due_date)->format('Y-m-d')) }}"
                           class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('due_date') ? 'border-red-400 bg-red-50' : 'border-slate-300' }}">
                    @error('due_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-start-2"></div>
            </div>

            <div>
                <label for="notes" class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Any special instructions..." class="w-full py-2 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ old('notes', $order->notes) }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm shadow-indigo-500/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Update Order
            </button>
            <a href="{{ route('orders.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800 bg-white border border-slate-300 hover:border-slate-400 rounded-xl transition">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function editOrder() {
    return {
        customer_id: {{ json_encode((int) $order->customer_id) }},
        members: @json($order->customer?->members ?? []),
        allServices: @json($allServices),
        measuredIds: [],
        loadingServices: true,
        form: {
            member_id: "{{ old('member_id', $order->member_id) ?: '' }}",
            service_id: "{{ (string) old('service_id', $order->service_id) }}",
            quantity: "{{ old('quantity', $order->quantity) }}",
            price: "{{ old('price', $order->price) }}",
            paid_amount: "{{ old('paid_amount', $order->paid_amount) }}",
        },

        async init() {
            await this.loadMeasured();
            if (!this.availableServices.some(s => s.id == this.form.service_id)) {
                const first = this.availableServices[0];
                this.form.service_id = first ? String(first.id) : '';
                if (first) this.form.price = String(first.price);
            }
            this.loadingServices = false;
        },

        get availableServices() {
            const list = this.allServices.filter(s => this.measuredIds.includes(Number(s.id)));
            const curId = Number(this.form.service_id);
            const cur = this.allServices.find(s => Number(s.id) === curId);
            if (curId && cur && !list.some(s => Number(s.id) === curId)) list.unshift(cur);
            return list;
        },

        async loadMeasured() {
            try {
                const params = new URLSearchParams({ customer_id: this.customer_id });
                if (this.form.member_id) params.append('member_id', this.form.member_id);
                const r = await fetch('{{ route("api.orders.memberServices") }}?' + params, { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                this.measuredIds = Array.isArray(d) ? d.map(s => Number(s.id)) : [];
            } catch (e) {
                this.measuredIds = [];
            }
        },

        async onMemberChange() {
            this.loadingServices = true;
            await this.loadMeasured();
            if (!this.availableServices.some(s => s.id == this.form.service_id)) {
                const first = this.availableServices[0];
                this.form.service_id = first ? String(first.id) : '';
                if (first) this.form.price = String(first.price);
            }
            this.loadingServices = false;
        },

        onServiceChange() {
            const s = this.allServices.find(x => Number(x.id) === Number(this.form.service_id));
            if (s) this.form.price = String(s.price);
        },

        lineTotal() {
            return (parseFloat(this.form.price) || 0) * (parseInt(this.form.quantity) || 0);
        },

        due() {
            return Math.max(0, this.lineTotal() - (parseFloat(this.form.paid_amount) || 0));
        },

        money(n) {
            return (Number(n) || 0).toLocaleString('en-PK');
        },
    };
}
</script>
@endpush