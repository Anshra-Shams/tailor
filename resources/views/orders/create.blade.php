<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Prowave') }} - New Order</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Figtree', sans-serif; }
    </style>
</head>
<body class="antialiased bg-slate-50 min-h-screen">

    <!-- Top Bar -->
    <header class="bg-white border-b border-slate-200 h-14 flex items-center px-4 sm:px-6 sticky top-0 z-30">
        <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            Orders
        </a>
        <div class="w-px h-5 bg-slate-200 mx-3"></div>
        <h1 class="text-sm font-semibold text-slate-800">New Order</h1>
    </header>

    <!-- Wizard Card -->
    <div class="max-w-xl mx-auto mt-6 sm:mt-10 mb-10 px-4" x-data="wizard()" x-init="init()">

        {{-- Flash --}}
        @if (session('success'))
            <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

            {{-- Progress Bar --}}
            <div class="flex gap-1.5 px-6 pt-5 pb-1">
                <template x-for="i in 3">
                    <div class="flex-1 h-1 rounded-full transition-colors duration-300"
                         :class="step >= i ? 'bg-indigo-500' : 'bg-slate-200'"></div>
                </template>
            </div>

            <div class="px-6 pb-6 pt-3">

                {{-- ═══════ STEP 1: Customer & Member ═══════ --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-xs text-slate-400 font-medium mb-1">Step 1 of 3</p>
                    <h2 class="text-base font-bold text-slate-800 mb-4">Select customer &amp; member</h2>

                    {{-- Customer --}}
                    <label class="block text-sm font-medium text-slate-600 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select x-model="form.customer_id" @change="onCustomerChange()" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 mb-4">
                        <option value="">— select customer —</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" data-members="{{ $customer->members->toJson() }}" data-gender="{{ $customer->gender }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>

                    {{-- Members --}}
                    <div x-show="form.customer_id">
                        <label class="block text-sm font-medium text-slate-600 mb-2">Member <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="m in members" :key="m.id">
                                <button type="button"
                                    @click="selectMember(m)"
                                    class="px-3.5 py-2 text-sm rounded-lg border-2 font-medium transition-all duration-150"
                                    :class="form.member_id === m.id ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'">
                                    <span x-text="m.name"></span>
                                    <span class="ml-1 text-xs opacity-60" x-text="m.gender"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step1" x-text="errors.step1" x-transition></p>
                </div>

                {{-- ═══════ STEP 2: Garment Type ═══════ --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-xs text-slate-400 font-medium mb-1">Step 2 of 3</p>
                    <h2 class="text-base font-bold text-slate-800 mb-1">Select garment type</h2>
                    <p class="text-xs text-slate-400 mb-4">For <span x-text="memberName()" class="font-medium text-slate-500"></span></p>

                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="svc in services" :key="svc.id">
                            <button type="button"
                                @click="selectService(svc)"
                                class="text-left p-3.5 rounded-xl border-2 transition-all duration-150"
                                :class="form.service_id === svc.id ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="text-sm font-semibold text-slate-700" x-text="svc.name"></div>
                                <div class="text-xs text-slate-400 mt-1">
                                    <span x-text="measurementFields(svc.id).length + ' fields'"></span> ·
                                    <span class="text-indigo-500 font-medium" x-text="measurementFields(svc.id).filter(f=>f.req).length + ' required'"></span>
                                </div>
                                <div class="text-xs text-slate-500 mt-1 font-medium" x-text="'Rs ' + Number(svc.price).toLocaleString()"></div>
                            </button>
                        </template>
                    </div>

                    <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step2" x-text="errors.step2" x-transition></p>
                </div>

                {{-- ═══════ STEP 3: Measurements ═══════ --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <p class="text-xs text-slate-400 font-medium mb-1">Step 3 of 3</p>
                    <h2 class="text-base font-bold text-slate-800 mb-1" x-text="serviceName() + ' measurements'"></h2>
                    <p class="text-xs text-slate-400 mb-3">Fields change by garment type. <span class="text-red-500">*</span> required to save.</p>

                    {{-- Loading --}}
                    <div x-show="loading" class="flex items-center gap-2 text-sm text-slate-500 mb-3 py-4 justify-center">
                        <svg class="animate-spin w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Loading previous measurements...
                    </div>

                    {{-- Previous measurements banner --}}
                    <div x-show="prevMeasure && !loading" x-transition class="mb-4 bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2.5 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-xs text-indigo-700">
                            <span class="font-semibold">Previous measurements loaded</span> from Order #<span x-text="prevMeasure?.order_id"></span>
                            <span class="text-indigo-500">(<span x-text="prevMeasure?.order_date"></span>)</span> — you can edit before saving.
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                        <template x-for="f in currentFields()" :key="f.key">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">
                                    <span x-text="f.label"></span>
                                    <span x-show="f.req" class="text-red-500">*</span>
                                    <span x-show="!f.req" class="text-slate-400 font-normal">(optional)</span>
                                </label>
                                <input type="text"
                                    :data-key="f.key"
                                    x-model="form.measurements[f.key]"
                                    placeholder="in inches"
                                    class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    :class="fieldError(f.key) ? 'border-red-400 bg-red-50' : ''">
                            </div>
                        </template>
                    </div>

                    {{-- Price & Due Date --}}
                    <div class="grid grid-cols-2 gap-x-4 mt-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Price (Rs) <span class="text-red-500">*</span></label>
                            <input type="number" x-model="form.price" min="0" step="50"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Due date</label>
                            <input type="date" x-model="form.due_date"
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Notes</label>
                        <textarea x-model="form.notes" rows="2" placeholder="Special instructions..."
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>

                    <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step3" x-text="errors.step3" x-transition></p>
                </div>

                {{-- ═══════ SUCCESS ═══════ --}}
                <div x-show="step === 4" class="text-center py-8" x-transition>
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800 mb-1">Order Created!</h2>
                    <p class="text-sm text-slate-500 mb-6">Redirecting to order details...</p>
                    <a href="{{ route('orders.index') }}" class="inline-block px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition">View All Orders</a>
                </div>

                {{-- Navigation --}}
                <div class="flex justify-between mt-6" x-show="step < 4">
                    <button type="button" @click="prev()"
                        x-show="step > 1"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        Back
                    </button>
                    <div x-show="step === 1"></div>

                    <button type="button" @click="next()"
                        class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm">
                        <span x-text="step === 3 ? 'Place Order' : 'Next'"></span>
                        <svg x-show="step < 3" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
            </div>
        </div>
        {{-- Hidden form for submission --}}
        <form id="orderForm" method="POST" action="{{ route('orders.store') }}" style="display:none">
            @csrf
            <input type="hidden" name="customer_id"   :value="form.customer_id">
            <input type="hidden" name="member_id"     :value="form.member_id === '__self__' ? '' : form.member_id">
            <input type="hidden" name="service_id"    :value="form.service_id">
            <input type="hidden" name="price"         :value="form.price">
            <input type="hidden" name="due_date"      :value="form.due_date">
            <input type="hidden" name="notes"         :value="form.notes">
            <input type="hidden" name="measurements"  :value="JSON.stringify(form.measurements)">
        </form>
    </div>

    <script>
    const GARMENTS = {
        'Shalwar Kameez': {
            fields: [
                { key: 'kurta_length',  label: 'Kurta Length',   req: true },
                { key: 'chest',         label: 'Chest',          req: true },
                { key: 'shoulder',      label: 'Shoulder',       req: true },
                { key: 'sleeve_len',    label: 'Sleeve Length',  req: true },
                { key: 'neck',          label: 'Neck',           req: false },
                { key: 'shalwar_len',   label: 'Shalwar Length', req: true },
                { key: 'pancha',        label: 'Pancha',         req: false },
            ],
        },
        'Suit Stitching': {
            fields: [
                { key: 'chest',         label: 'Chest',          req: true },
                { key: 'waist',         label: 'Waist',          req: true },
                { key: 'shoulder',      label: 'Shoulder',       req: true },
                { key: 'sleeve_len',    label: 'Sleeve Length',  req: true },
                { key: 'collar',        label: 'Collar',         req: false },
                { key: 'jacket_len',    label: 'Jacket Length',  req: true },
                { key: 'trouser_len',   label: 'Trouser Length', req: true },
                { key: 'trouser_waist', label: 'Trouser Waist',  req: true },
            ],
        },
        'Shirt Stitching': {
            fields: [
                { key: 'chest',      label: 'Chest',         req: true },
                { key: 'shoulder',   label: 'Shoulder',      req: true },
                { key: 'sleeve_len', label: 'Sleeve Length', req: true },
                { key: 'neck',       label: 'Neck',          req: true },
                { key: 'shirt_len',  label: 'Shirt Length',  req: true },
            ],
        },
        'Trouser': {
            fields: [
                { key: 'waist',      label: 'Waist',         req: true },
                { key: 'length',     label: 'Length',        req: true },
                { key: 'thigh',      label: 'Thigh',         req: false },
                { key: 'knee',       label: 'Knee',          req: false },
                { key: 'pancha',     label: 'Pancha',        req: true },
            ],
        },
        'Waistcoat': {
            fields: [
                { key: 'chest',      label: 'Chest',         req: true },
                { key: 'waist',      label: 'Waist',         req: true },
                { key: 'shoulder',   label: 'Shoulder',      req: true },
                { key: 'length',     label: 'Length',        req: true },
                { key: 'neck',       label: 'Neck',          req: false },
            ],
        },
        'Sherwani': {
            fields: [
                { key: 'chest',      label: 'Chest',         req: true },
                { key: 'waist',      label: 'Waist',         req: true },
                { key: 'shoulder',   label: 'Shoulder',      req: true },
                { key: 'sleeve_len', label: 'Sleeve Length', req: true },
                { key: 'collar',     label: 'Collar',        req: false },
                { key: 'length',     label: 'Length',        req: true },
                { key: 'neck',       label: 'Neck',          req: false },
            ],
        },
        'Lehenga': {
            fields: [
                { key: 'bust',       label: 'Bust',          req: true },
                { key: 'waist',      label: 'Waist',         req: true },
                { key: 'hip',        label: 'Hip',           req: false },
                { key: 'length',     label: 'Lehenga Length', req: true },
                { key: 'blouse_len', label: 'Blouse Length', req: true },
                { key: 'shoulder',   label: 'Shoulder',      req: true },
                { key: 'sleeve_len', label: 'Sleeve Length', req: false },
            ],
        },
        'Alteration': {
            fields: [
                { key: 'description', label: 'What to alter',  req: true },
                { key: 'chest',       label: 'Chest',          req: false },
                { key: 'waist',       label: 'Waist',          req: false },
                { key: 'length',      label: 'Length',         req: false },
            ],
        },
    };

    const DEFAULT_FIELDS = [
        { key: 'chest',      label: 'Chest',      req: true },
        { key: 'waist',      label: 'Waist',      req: false },
        { key: 'shoulder',   label: 'Shoulder',   req: true },
        { key: 'length',     label: 'Length',     req: true },
        { key: 'sleeve_len', label: 'Sleeve Length', req: false },
    ];

    function wizard() {
        return {
            step: 1,
            loading: false,
            customers: @json($customers),
            services: [],
            members: [],
            prevMeasure: null,
            form: {
                customer_id: '',
                member_id: '',
                service_id: '',
                price: '',
                due_date: '',
                notes: '',
                measurements: {},
            },
            errors: { step1: '', step2: '', step3: '' },
            fieldErrors: {},

            async init() {
                const res = await fetch('{{ route("api.orders.services") }}');
                this.services = await res.json();
            },

            onCustomerChange() {
                const opt = document.querySelector(`select option[value="${this.form.customer_id}"]`);
                if (opt && opt.dataset.members) {
                    const customerName = opt.textContent.trim();
                    const customerGender = opt.dataset.gender || '';
                    const subMembers = JSON.parse(opt.dataset.members);
                    this.members = [
                        { id: '__self__', name: customerName + ' (customer)', gender: customerGender },
                        ...subMembers
                    ];
                } else {
                    this.members = [];
                }
                this.form.member_id = '';
            },

            selectMember(m) {
                this.form.member_id = this.form.member_id === m.id ? '' : m.id;
                this.errors.step1 = '';
            },

            selectService(svc) {
                this.form.service_id = svc.id;
                this.form.price = svc.price;
                this.errors.step2 = '';
            },

            memberName() {
                if (this.form.member_id === '__self__') {
                    const opt = document.querySelector(`select option[value="${this.form.customer_id}"]`);
                    return opt ? opt.textContent.trim() : '';
                }
                const m = this.members.find(x => x.id == this.form.member_id);
                return m ? m.name : '';
            },

            serviceName() {
                const s = this.services.find(x => x.id == this.form.service_id);
                return s ? s.name : 'Measurements';
            },

            measurementFields(svcId) {
                const s = this.services.find(x => x.id == svcId);
                if (!s) return [];
                return GARMENTS[s.name]?.fields || DEFAULT_FIELDS;
            },

            currentFields() {
                return this.measurementFields(this.form.service_id);
            },

            fieldError(key) {
                return this.fieldErrors[key] || false;
            },

            async loadPrevious() {
                this.prevMeasure = null;
                const memberId = this.form.member_id === '__self__' ? '' : this.form.member_id;
                const params = new URLSearchParams({
                    customer_id: this.form.customer_id,
                    service_id: this.form.service_id,
                    member_id: memberId,
                });
                try {
                    const res = await fetch('{{ route("api.orders.prevMeasurements") }}?' + params.toString());
                    const data = await res.json();
                    if (data.measurements) {
                        this.prevMeasure = data;
                        this.form.measurements = { ...data.measurements };
                    } else {
                        this.form.measurements = {};
                    }
                } catch (e) {
                    this.form.measurements = {};
                }
            },

            async next() {
                if (this.step === 1) {
                    if (!this.form.customer_id) { this.errors.step1 = 'Select a customer.'; return; }
                    if (!this.form.member_id)    { this.errors.step1 = 'Select a member.'; return; }
                    this.errors.step1 = '';
                    this.step = 2;
                } else if (this.step === 2) {
                    if (!this.form.service_id) { this.errors.step2 = 'Pick a garment type.'; return; }
                    this.errors.step2 = '';
                    this.loading = true;
                    this.step = 3;
                    await this.loadPrevious();
                    this.loading = false;
                } else if (this.step === 3) {
                    if (!this.form.price || this.form.price <= 0) { this.errors.step3 = 'Enter a valid price.'; return; }

                    this.fieldErrors = {};
                    let ok = true;
                    this.currentFields().forEach(f => {
                        if (f.req && !this.form.measurements[f.key]?.trim()) {
                            this.fieldErrors[f.key] = true;
                            ok = false;
                        }
                    });
                    if (!ok) { this.errors.step3 = 'Fill all required fields.'; return; }

                    this.errors.step3 = '';
                    document.getElementById('orderForm').submit();
                }
            },

            prev() {
                if (this.step > 1) this.step--;
            },
        };
    }
    </script>
</body>
</html>
