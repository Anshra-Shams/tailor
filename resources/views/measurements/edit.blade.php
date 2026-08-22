@extends('layouts.app')

@section('title', 'Edit Measurement')

@section('content')
<div x-data="editor()" x-init="init()" class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Edit Measurement</h2>
        <p class="text-sm text-slate-500 mt-1">Customer: <span class="font-semibold text-slate-700" x-text="selectedMemberName()"></span></p>
    </div>

    {{-- Section: Select Service --}}
    <section>
        <div class="flex items-center gap-3 mb-4">
            <h3 class="text-base font-bold text-slate-800 flex-shrink-0">Select Service</h3>
            <div class="flex-1"></div>
            <div class="relative flex-shrink-0">
                <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </div>
                <input type="text" x-model="serviceSearchQuery" placeholder="Search services..." class="w-56 pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-400">
            </div>
            <button type="button" @click="servicePrev()" :disabled="servicePage === 0" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
            </button>
            <button type="button" @click="serviceNext()" :disabled="servicePage >= serviceMaxPage" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>

        {{-- Service cards slider --}}
        <div class="overflow-hidden rounded-xl" style="height: 88px;">
            <div class="flex gap-4 transition-transform duration-300 ease-in-out h-full" :style="'transform: translateX(-' + (servicePage * 100) + '%)'">
                <template x-for="(svc, idx) in filteredServices" :key="svc.id">
                    <button type="button" @click="selectService(svc)" class="flex-shrink-0 w-[calc(25%-0.75rem)] h-full text-left p-4 rounded-xl border transition-all duration-150" :class="form.service_id == svc.id ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                        <div class="text-sm font-semibold text-slate-700" x-text="svc.name"></div>
                        <div class="text-xs text-slate-400 mt-1.5">
                            <span x-text="fieldsFor(svc.id).length + ' fields'"></span> &middot;
                            <span class="text-indigo-500 font-medium" x-text="fieldsFor(svc.id).filter(f=>f.req).length + ' required'"></span>
                        </div>
                    </button>
                </template>
            </div>
            <p x-show="filteredServices.length === 0" class="text-sm text-slate-400 py-4 text-center">No services found.</p>
        </div>
        <p class="mt-2 text-sm text-red-500 min-h-[20px]" x-show="errors.service" x-text="errors.service" x-transition></p>
    </section>

    {{-- Measurements --}}
    <div x-show="form.service_id" x-transition class="space-y-6">
        <section class="space-y-3">
            <div x-show="loading" class="flex items-center gap-2 text-sm text-slate-400 py-2 justify-center">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Loading...
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-base font-bold text-slate-800 mb-4" x-text="serviceName() + ' Measurements'"></h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-4">
                    <template x-for="f in currentFields()" :key="f.k">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">
                                <span x-text="f.l"></span>
                                <span x-show="f.req" class="text-red-500">*</span>
                                <span x-show="!f.req" class="text-slate-400 font-normal">(optional)</span>
                            </label>
                            <input type="text" x-model="form.measurements[f.k]" placeholder="in inches" class="w-full py-2 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" :class="fieldErr(f.k) ? 'border-red-400 bg-red-50' : ''">
                        </div>
                    </template>
                </div>
            </div>
        </section>

        {{-- Special Instructions --}}
        <section>
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-base font-bold text-slate-800 mb-4">Special Instructions</h3>
                <textarea x-model="form.notes" rows="2" placeholder="Any special instructions or notes..." class="w-full py-2 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
            </div>
        </section>

        {{-- Error + Action buttons --}}
        <p class="text-sm text-red-500 min-h-[20px]" x-show="errors.general" x-text="errors.general" x-transition></p>

        <div class="flex items-center gap-3">
            <a href="{{ route('measurements.show', $measurement) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                Cancel
            </a>
            <button type="button" @click="submitForm()" :disabled="submitting" class="ml-auto inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition disabled:opacity-50">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                <span x-text="submitting ? 'Updating...' : 'Update Measurement'"></span>
            </button>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="editForm" method="POST" action="{{ route('measurements.update', $measurement) }}" style="display:none">
        @csrf @method('PUT')
        <input type="hidden" name="customer_id"  :value="form.customer_id">
        <input type="hidden" name="member_id"    :value="form.member_id === '__self__' ? '' : form.member_id">
        <input type="hidden" name="service_id"   :value="form.service_id">
        <input type="hidden" name="notes"        :value="form.notes">
        <input type="hidden" name="measurements" :value="JSON.stringify(form.measurements)">
    </form>
</div>
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]').content;

function editor() {
    return {
        loading: false,
        submitting: false,
        services: [],
        serviceSearchQuery: '',
        servicePage: 0,
        selectedCustomer: null,
        selectedMember: null,
        form: {
            customer_id: {{ $measurement->customer_id }},
            member_id: @json($measurement->member_id ?? ''),
            service_id: {{ $measurement->service_id }},
            notes: @js($measurement->notes ?? ''),
            measurements: @js($measurement->data ?? [])
        },
        errors: { service:'', general:'' },

        get filteredServices() {
            const q = this.serviceSearchQuery.trim().toLowerCase();
            if (!q) return this.services;
            return this.services.filter(s => s.name.toLowerCase().includes(q));
        },

        get serviceMaxPage() {
            return Math.max(0, Math.ceil(this.filteredServices.length / 4) - 1);
        },

        serviceNext() {
            if (this.servicePage < this.serviceMaxPage) this.servicePage++;
        },

        servicePrev() {
            if (this.servicePage > 0) this.servicePage--;
        },

        async init() {
            this.$watch('serviceSearchQuery', () => { this.servicePage = 0; });
            try {
                this.services = await (await fetch('{{ route("api.orders.services") }}', { headers: { 'Accept': 'application/json' } })).json();
            } catch (e) {
                this.services = [];
            }
            this.selectedCustomer = { id: this.form.customer_id, name: '{{ $measurement->customer->name ?? '' }}', phone: '{{ $measurement->customer->phone ?? '' }}' };
            if (this.form.member_id) {
                this.selectedMember = { id: this.form.member_id, name: '{{ $measurement->member->name ?? "" }}', relation: '{{ $measurement->member->relation ?? "" }}' };
            } else {
                this.selectedMember = { id: '__self__', name: '{{ $measurement->customer->name ?? "" }}', relation: '' };
            }
        },

        selectedMemberName() {
            if (!this.selectedMember) return '';
            if (this.selectedMember.id === '__self__') return this.selectedMember.name + ' (customer)';
            let n = this.selectedMember.name;
            if (this.selectedMember.relation) n += ' (' + this.selectedMember.relation + ')';
            return n;
        },

        async selectService(svc) {
            this.form.service_id = svc.id;
            this.errors.service = '';
        },

        fieldsFor(svcId) {
            const s = this.services.find(x => x.id == svcId);
            if (!s || !s.measurement_fields || s.measurement_fields.length === 0) return [];
            return s.measurement_fields.map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
        },

        currentFields() {
            return this.fieldsFor(this.form.service_id);
        },

        serviceName() {
            const s = this.services.find(x => x.id == this.form.service_id);
            return s ? s.name : 'Measurements';
        },

        fieldErr(k) {
            return false;
        },

        async submitForm() {
            this.errors.service = '';
            this.errors.general = '';
            if (!this.form.service_id) { this.errors.service = 'Pick a service'; return; }
            let ok = true;
            this.currentFields().forEach(f => {
                if (f.req && !(this.form.measurements[f.k] || '').trim()) {
                    ok = false;
                }
            });
            if (!ok) { this.errors.general = 'Fill all required fields'; return; }
            this.submitting = true;
            await new Promise(res => setTimeout(res, 0));
            document.getElementById('editForm').submit();
        }
    };
}
</script>
@endpush
