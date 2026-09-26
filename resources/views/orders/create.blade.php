@extends('layouts.app')

@section('title', $editOrder ? 'Edit Order' : 'Create Order')

@section('back_button')
<a href="{{ route('orders.index') }}"
   class="group relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 transition mr-1"
   aria-label="Back to Orders">
    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    {{-- Tooltip on hover --}}
    <span class="absolute left-1/2 -translate-x-1/2 top-full mt-1.5 px-2 py-0.5 text-[10px] font-semibold text-white bg-slate-800 rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Back to Orders
    </span>
</a>
@endsection

@section('content')
<div x-data="orderWizard()" x-init="init()" class="space-y-6 -mt-4 sm:-mt-5 lg:-mt-6">

    {{-- Server Error Banner --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-red-800">Please correct the errors below</h4>
            <ul class="mt-1 text-xs text-red-600 list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Error Banner --}}
    <div x-show="errors.general" x-transition class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-red-800" x-text="errors.general"></h4>
            <p class="text-xs text-red-600 mt-0.5" x-show="errors.measurements" x-text="errors.measurements"></p>
        </div>
    </div>

    {{-- Main 2-Column Grid (Matches services/create & measurements/create) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ================= LEFT COLUMN: WORK AREA (8 Cols) ================= --}}
        <div class="lg:col-span-8 space-y-5">

            {{-- STEP 1: CUSTOMER + MEMBER + FINANCIAL LEDGER --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3.5">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Select Customer &amp; Family Member</h3>
                    </div>
                    <button type="button" @click="showQuickCustomer = true" x-show="!editMode"
                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + New Customer
                    </button>
                </div>

                {{-- Search Bar (when customer is not yet selected) --}}
                <div x-show="!selectedCustomer && !editMode" class="relative" @click.outside="showDropdown = false">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Search Customer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @focus="openDropdown()" @input.debounce.300ms="runSearch()"
                            placeholder="Search by customer name or phone number..."
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''; showDropdown = false"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Search results dropdown --}}
                    <div x-show="showDropdown" x-transition class="absolute left-0 right-0 z-30 mt-2 bg-white border border-slate-200 rounded-xl shadow-xl max-h-80 overflow-y-auto divide-y divide-slate-100">
                        <div x-show="searchLoading" class="flex items-center justify-center gap-2 text-sm text-slate-400 py-5">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Searching customer directory...
                        </div>

                        <template x-if="customers.length > 0">
                            <div>
                                <div class="px-4 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Customers</div>
                                <template x-for="c in customers" :key="'c'+c.id">
                                    <button type="button" @click="pickCustomer(c)" class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition" x-text="c.name?.charAt(0)?.toUpperCase()"></span>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700" x-text="c.name"></p>
                                                <p class="text-xs text-slate-400" x-text="c.phone"></p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-slate-400 group-hover:text-indigo-600 font-semibold flex items-center gap-1">Select <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-if="members.length > 0">
                            <div>
                                <div class="px-4 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Family Members</div>
                                <template x-for="m in members" :key="'m'+m.id">
                                    <button type="button" @click="pickMemberFromSearch(m)" class="w-full text-left px-4 py-2.5 hover:bg-purple-50 transition flex items-center justify-between group">
                                        <div class="flex items-center gap-3">
                                            <span class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition" x-text="m.name?.charAt(0)?.toUpperCase()"></span>
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800 group-hover:text-purple-700">
                                                    <span x-text="m.name"></span>
                                                    <span x-show="m.relation" class="ml-1 text-xs text-slate-400 capitalize" x-text="'(' + m.relation + ')'"></span>
                                                </p>
                                                <p class="text-xs text-slate-400">Customer: <span class="font-medium text-slate-500" x-text="m.customer?.name"></span></p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-slate-400 group-hover:text-purple-600 font-semibold flex items-center gap-1">Select <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <p x-show="!searchLoading && customers.length === 0 && members.length === 0" class="px-4 py-8 text-center text-sm text-slate-400">No customers or members found.</p>
                    </div>
                </div>

                {{-- Selected Customer Card --}}
                <div x-show="selectedCustomer" x-transition class="rounded-xl bg-slate-50 border border-slate-200/80 overflow-hidden">

                    {{-- Row 1: Customer Info + Order For chips + X button --}}
                    <div class="flex items-start gap-3 px-4 pt-3 pb-2.5">

                        {{-- Avatar --}}
                        <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5"
                            x-text="selectedCustomer?.name?.charAt(0)?.toUpperCase()"></span>

                        {{-- Name + Phone + Gender --}}
                        <div class="flex-shrink-0 min-w-0">
                            <h4 class="font-bold text-slate-800 text-sm leading-tight truncate" x-text="selectedCustomer?.name"></h4>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs text-slate-500 font-medium" x-text="selectedCustomer?.phone"></span>
                                <span x-show="selectedCustomer?.gender"
                                    class="capitalize px-1.5 py-0.5 rounded-full bg-slate-200/80 text-slate-600 text-[10px] font-medium"
                                    x-text="selectedCustomer?.gender"></span>
                            </div>
                        </div>

                        {{-- Add Family Member (+ before the divider) --}}
                        <button type="button" @click="showQuickMember = true" x-show="!editMode"
                            class="px-2 py-1 rounded-lg border border-dashed border-indigo-300 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition flex items-center flex-shrink-0 cursor-pointer ml-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                        </button>

                        {{-- Vertical divider --}}
                        <div class="w-px h-9 self-center bg-slate-300 mx-1 flex-shrink-0" aria-hidden="true"></div>

                        {{-- Order For label + chips + Add button (all wrap together) --}}
                        <div class="flex-1 flex flex-wrap items-center gap-1.5 min-w-0">
                            <span class="text-xs font-bold text-slate-600 flex-shrink-0 mr-0.5">Order For:</span>

                            <template x-for="m in memberOptions" :key="'mo'+m.id">
                                <button type="button" @click="pickMember(m)"
                                    class="px-2.5 py-1 rounded-lg border text-xs font-semibold transition-all flex items-center gap-1 flex-shrink-0"
                                    :class="isMemberSelected(m.id)
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/25'
                                        : 'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50'">
                                    <svg x-show="isMemberSelected(m.id)" class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                                    </svg>
                                    <span x-text="m.isSelf ? 'Self (' + selectedCustomer?.name + ')' : m.name"></span>
                                    <span class="opacity-60 font-normal" x-show="!m.isSelf && m.relation" x-text="'(' + m.relation + ')'"></span>
                                </button>
                            </template>
                        </div>

                        {{-- X / Change customer (top-right) --}}
                        <button type="button" @click="clearAll()" title="Change Customer" x-show="!editMode"
                            class="ml-auto flex-shrink-0 p-1 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Row 2: Ledger badges --}}
                    <div class="flex items-center gap-2 px-4 py-2 border-t border-slate-200/70 bg-white/60 flex-wrap">
                        <div x-show="ledger" x-transition class="flex flex-wrap items-center gap-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-xs font-medium text-slate-600">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                </svg>
                                <span x-text="(ledger?.total_orders ?? 0) + ' past orders'"></span>
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-xs font-medium text-slate-600">
                                Total: <strong class="ml-1 text-slate-800" x-text="money(ledger?.total_amount)"></strong>
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-xs font-medium text-emerald-700">
                                Paid: <strong class="ml-1" x-text="money(ledger?.paid_amount)"></strong>
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold"
                                :class="parseFloat(ledger?.due_amount) > 0 ? 'bg-red-50 text-red-600 border border-red-200' : 'bg-slate-100 text-slate-500'">
                                Due: <strong class="ml-1" x-text="money(ledger?.due_amount)"></strong>
                            </span>
                        </div>
                        <div x-show="!ledger" class="text-xs text-slate-400 italic">Loading...</div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: ORDER ITEMS & MEASUREMENTS TABLE --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-3.5" x-show="isMemberSelected()" x-transition>
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-100 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Order Items &amp; Measurements</h3>
                        <span x-show="selectedServices.length > 0"
                            class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100"
                            x-text="selectedServices.length + ' Item' + (selectedServices.length > 1 ? 's' : '')"></span>
                    </div>
                    <button type="button" @click="addFirstAvailableService()" x-show="services.length > 0"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Service
                    </button>
                </div>

                {{-- Loading services indicator --}}
                <div x-show="servicesLoading" class="flex items-center justify-center gap-2 text-xs text-slate-400 py-6">
                    <svg class="animate-spin w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Loading services...</span>
                </div>

                {{-- Empty state when no services added --}}
                <div x-show="!servicesLoading && selectedServices.length === 0" class="text-center py-8 border-2 border-dashed border-slate-200 rounded-xl space-y-2">
                    <p class="text-xs text-slate-500 font-medium">No services added to this order yet.</p>
                    <button type="button" @click="addFirstAvailableService()" :disabled="services.length === 0"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-xs disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Add First Service
                    </button>
                </div>

                {{-- Table with Service Dropdown in each row --}}
                <div class="overflow-x-auto" x-show="!servicesLoading && selectedServices.length > 0">
                    <table class="w-full border-collapse text-sm" style="min-width:680px">
                        <thead>
                            <tr class="border-b border-slate-200 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-3 py-2.5 w-[240px]">Service</th>
                                <th class="px-2 py-2.5 w-[180px]">Note</th>
                                <th class="px-2 py-2.5 text-center w-[48px]">Qty</th>
                                <th class="px-2 py-2.5 text-center w-[160px]">Unit Price</th>
                                <th class="px-2 py-2.5 text-right w-[110px]">Total</th>
                                <th class="px-2 py-2.5 text-center w-[52px]">Meas</th>
                                <th class="px-2 py-2.5 text-center w-[52px]">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="svc in selectedServices" :key="'sel'+svc.id">
                                <tr class="hover:bg-slate-50/60 transition">
                                    {{-- Service Direct Search Input & Dropdown --}}
                                    <td class="px-3 py-3 align-middle">
                                        <div class="relative" @click.outside="closeServiceDropdown(svc.id)">
                                            {{-- Direct Search Input Field --}}
                                            <div class="relative">
                                                <input type="text"
                                                    :id="'svc_input_' + svc.id"
                                                    :value="activeServiceDropdown === svc.id ? (serviceFilterQuery[svc.id] ?? '') : (svc.name ? svc.name + (svc.days ? ' (' + svc.days + ' Days)' : '') : '')"
                                                    @focus="openServiceDropdown(svc.id, svc)"
                                                    @input="serviceFilterQuery[svc.id] = $event.target.value; activeServiceDropdown = svc.id"
                                                    placeholder="Search service..."
                                                    class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition cursor-pointer">
                                                
                                                {{-- Clear or dropdown indicator icon --}}
                                                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-150"
                                                        :class="activeServiceDropdown === svc.id ? 'rotate-180 text-indigo-600' : ''"
                                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            {{-- Floating Available Services Dropdown (fixed overlay so it never scrolls the card) --}}
                                            <div x-show="activeServiceDropdown === svc.id" x-transition
                                                :style="dropdownStyle"
                                                class="fixed z-[80] min-w-[240px] bg-white rounded-xl border border-slate-200 shadow-xl overflow-hidden divide-y divide-slate-100 max-h-56 overflow-y-auto">
                                                <template x-for="s in getFilteredServicesFor(svc.id)" :key="'sopt_'+s.id">
                                                    <button type="button" @mousedown.prevent="pickServiceFromDropdown(svc.id, s.id)"
                                                        :class="s.id === svc.id ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium'"
                                                        class="w-full text-left px-3 py-2.5 text-xs flex items-center justify-between transition group cursor-pointer">
                                                        <span class="truncate" x-text="s.name + (s.days ? ' (' + s.days + ' Days)' : '')"></span>
                                                        <span x-show="s.id === svc.id" class="text-indigo-600 text-xs font-bold">✓</span>
                                                    </button>
                                                </template>
                                                <div x-show="getFilteredServicesFor(svc.id).length === 0" class="px-3 py-4 text-center text-xs text-slate-400">
                                                    No matching services found
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Note input --}}
                                    <td class="px-2 py-3 align-middle">
                                        <input type="text" x-model="serviceNotes[svc.id]" placeholder="Add note..."
                                            class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </td>

                                    {{-- Qty --}}
                                    <td class="px-2 py-3 text-center align-middle">
                                        <input type="number" min="1" step="1" x-model.number="serviceQty[svc.id]"
                                            class="w-10 py-1.5 px-1 rounded-lg border border-slate-200 bg-white text-xs font-bold text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="px-2 py-3 text-right align-middle">
                                        <div class="relative w-full" @click.outside="closePriceDropdown(svc.id)">
                                            <div class="flex items-center gap-1 w-full">
                                                <div class="relative flex-1 min-w-0">
                                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[10px] font-semibold text-slate-400 pointer-events-none select-none">Rs.</span>
                                                    <input type="number" min="0" step="1" x-model.number="servicePrices[svc.id]"
                                                        class="w-full pl-7 pr-1.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-right focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                                                </div>

                                                {{-- Basic / Standard / Premium tier button --}}
                                                <button type="button" :id="'tier_btn_' + svc.id"
                                                    class="w-7 h-7 flex-shrink-0 inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-indigo-600 hover:border-indigo-300 transition cursor-pointer"
                                                    @click="activePriceDropdown === svc.id ? closePriceDropdown(svc.id) : openPriceDropdown(svc.id)"
                                                    :title="getTierLabel(getServiceTier(svc.id)) + ' tier'">
                                                    <svg class="w-3.5 h-3.5 transition-transform duration-150"
                                                        :class="activePriceDropdown === svc.id ? 'rotate-180 text-indigo-600' : ''"
                                                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Floating price tier dropdown --}}
                                            <div x-show="activePriceDropdown === svc.id" x-transition
                                                :style="priceDropdownStyle"
                                                class="fixed z-[80] min-w-[150px] bg-white rounded-xl border border-slate-200 shadow-xl overflow-hidden divide-y divide-slate-100 py-1">
                                                <template x-for="t in ['basic', 'standard', 'premium']" :key="'tier_' + svc.id + '_' + t">
                                                    <button type="button" @mousedown.prevent="pickPriceTierFromDropdown(svc.id, t)"
                                                        class="w-full text-left px-3 py-2 text-xs flex items-center justify-between transition cursor-pointer"
                                                        :class="getServiceTier(svc.id) === t ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50 font-medium'">
                                                        <span x-text="getTierLabel(t)"></span>
                                                        <span class="font-bold" x-text="money(getTierPrice(getSvcById(svc.id), t))"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-2 py-3 text-right font-extrabold text-slate-800 text-sm align-middle whitespace-nowrap"
                                        x-text="money((parseFloat(servicePrices[svc.id]) || 0) * (parseInt(serviceQty[svc.id]) || 1))"></td>

                                    {{-- Measurements Button --}}
                                    <td class="px-2 py-3 text-center align-middle whitespace-nowrap">
                                        <button type="button" @click="goMeasurements(svc)"
                                            title="Measurements"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full transition cursor-pointer hover:opacity-80"
                                            :class="mStatusClass(svc.id)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </button>
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-2 py-3 text-center align-middle">
                                        <button type="button" @click="removeService(svc.id)" title="Remove service"
                                            class="w-7 h-7 inline-flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- ================= RIGHT COLUMN: STICKY CHECKOUT SIDEBAR (4 Cols) ================= --}}
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-6 space-y-5">

                {{-- Checkout Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Order Summary</span>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400" x-text="selectedServices.length + ' Items'"></span>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Empty state --}}
                        <div x-show="selectedServices.length === 0" class="text-center py-6 text-slate-400 space-y-2">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            <p class="text-xs font-medium">Select customer and services on the left to configure order checkout.</p>
                        </div>

                        {{-- Line items breakdown --}}
                        <div x-show="selectedServices.length > 0" class="space-y-4">
                            <div class="rounded-xl bg-slate-50 border border-slate-200/80 divide-y divide-slate-100 text-xs overflow-hidden">
                                <template x-for="svc in selectedServices" :key="'sum'+svc.id">
                                    <div class="flex items-center justify-between px-3.5 py-2.5">
                                        <div class="truncate pr-2">
                                            <div class="text-slate-800 font-semibold truncate flex items-center gap-1.5">
                                                <span x-text="svc.name"></span>
                                                <span class="text-[10px] px-1.5 py-0.2 rounded font-bold uppercase tracking-wider"
                                                    :class="getServiceTier(svc.id) === 'premium' ? 'bg-purple-100 text-purple-700' : (getServiceTier(svc.id) === 'basic' ? 'bg-slate-200 text-slate-700' : 'bg-indigo-100 text-indigo-700')"
                                                    x-text="getServiceTier(svc.id)"></span>
                                            </div>
                                            <span class="text-slate-400 font-normal text-[11px]">
                                                <span x-text="money(servicePrices[svc.id])"></span> &times; <span x-text="serviceQty[svc.id] || 1"></span>
                                            </span>
                                        </div>
                                        <span class="font-bold text-slate-800 whitespace-nowrap"
                                            x-text="money((parseFloat(servicePrices[svc.id]) || 0) * (parseInt(serviceQty[svc.id]) || 1))"></span>
                                    </div>
                                </template>
                                <div class="flex items-center justify-between px-3.5 py-2.5 bg-indigo-50/50 text-sm">
                                    <span class="font-bold text-slate-800">Total Order Amount</span>
                                    <span class="font-extrabold text-indigo-700 text-base" x-text="money(totalPrice)"></span>
                                </div>
                            </div>

                            {{-- Payment and Delivery inputs --}}
                            <div class="space-y-3 pt-1">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Delivery Due Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" :min="today" x-model="form.due_date"
                                        class="w-full py-2 px-3 rounded-xl border text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 transition"
                                        :class="errors.due_date ? 'border-red-500 ring-1 ring-red-500 bg-red-50/50' : 'border-slate-300'">
                                    <p x-show="errors.due_date" x-text="errors.due_date" class="text-[11px] font-semibold text-red-500 mt-1"></p>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Advance Payment Received (Rs.)
                                    </label>
                                    <input type="number" min="0" step="1" x-model="form.paid_amount" placeholder="0"
                                        class="w-full py-2 px-3 rounded-xl border text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500 transition"
                                        :class="errors.paid_amount ? 'border-red-500 ring-1 ring-red-500 bg-red-50/50' : 'border-slate-300'">
                                    <p x-show="errors.paid_amount" x-text="errors.paid_amount" class="text-[11px] font-semibold text-red-500 mt-1"></p>
                                </div>

                                <div x-show="!editMode" x-cloak>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Deposit Advance To <span class="text-red-500" x-show="(parseFloat(form.paid_amount) || 0) > 0">*</span>
                                    </label>
                                    <select x-model="form.account_id"
                                        class="w-full py-2 px-3 rounded-xl border text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-indigo-500 transition"
                                        :class="errors.account_id ? 'border-red-500 ring-1 ring-red-500 bg-red-50/50' : 'border-slate-300'">
                                        <option value="">-- Select Cash or Bank Account --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc['id'] }}">{{ $acc['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <p x-show="errors.account_id" x-text="errors.account_id" class="text-[11px] font-semibold text-red-500 mt-1"></p>
                                </div>

                                {{-- Remaining Due Banner --}}
                                <div class="rounded-xl px-4 py-2.5 flex items-center justify-between transition-all"
                                    :class="remainingDue > 0 ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'">
                                    <span class="text-xs font-semibold" :class="remainingDue > 0 ? 'text-red-700' : 'text-emerald-700'"
                                        x-text="remainingDue > 0 ? 'Remaining Balance Due:' : 'Fully Paid / Clear'"></span>
                                    <span class="text-sm font-extrabold" :class="remainingDue > 0 ? 'text-red-700' : 'text-emerald-700'"
                                        x-text="money(remainingDue)"></span>
                                </div>

                                {{-- Special notes --}}
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Special Tailoring Remarks</label>
                                    <textarea x-model="form.notes" rows="2" placeholder="Urgent order, double stitching, specific delivery notes..."
                                        class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                                </div>
                            </div>

                            {{-- Visible Error Summary in Checkout Sidebar --}}
                            <div x-show="errors.general" x-transition class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700 space-y-1">
                                <div class="font-bold flex items-center gap-1.5 text-red-800">
                                    <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                    <span x-text="errors.general"></span>
                                </div>
                                <p x-show="errors.customer" x-text="'• ' + errors.customer" class="font-semibold text-red-600 pl-5"></p>
                                <p x-show="errors.service" x-text="'• ' + errors.service" class="font-semibold text-red-600 pl-5"></p>
                                <p x-show="errors.due_date" x-text="'• ' + errors.due_date" class="font-semibold text-red-600 pl-5"></p>
                                <p x-show="errors.account_id" x-text="'• ' + errors.account_id" class="font-semibold text-red-600 pl-5"></p>
                                <p x-show="errors.measurements" x-text="'• ' + errors.measurements" class="font-semibold text-red-600 pl-5"></p>
                            </div>

                            {{-- Submit button --}}
                            <button type="button" @click="saveOrder()" :disabled="submitting || !selectedCustomer || selectedServices.length === 0"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md shadow-indigo-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span x-text="submitting ? 'Saving Order...' : (editMode ? 'Update Order' : 'Complete & Save Order')"></span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= HIDDEN FORM FOR ACTUAL SUBMISSION ================= --}}
    <form id="orderForm" method="POST" action="{{ $editOrder ? route('orders.update', $editOrder) : route('orders.store') }}" style="display:none">
        @csrf
        @if($editOrder) @method('PUT') @endif
        <input type="hidden" name="customer_id" id="hidden_customer_id" :value="form.customer_id">
        <input type="hidden" name="member_id"   id="hidden_member_id"   :value="form.member_id">
        <input type="hidden" name="due_date"    id="hidden_due_date"    :value="form.due_date">
        <input type="hidden" name="notes"       id="hidden_notes"       :value="form.notes">
        <input type="hidden" name="paid_amount" id="hidden_paid_amount" :value="form.paid_amount || 0">
        <input type="hidden" name="account_id"  id="hidden_account_id"  :value="form.account_id">
        <div id="hidden_services_container"></div>
    </form>

    {{-- ================= MEASUREMENT MODAL (CATEGORIZED INTO UPPER & LOWER BODY) ================= --}}
    <div x-show="mModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="mModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl z-10 max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <span x-text="mService?.name + ' Measurements'"></span>
                        </h3>
                        <p class="text-xs text-slate-500">For: <span class="font-semibold text-slate-700" x-text="selectedMemberName() + ' (' + (selectedCustomer?.name || '') + ')'"></span></p>
                    </div>
                </div>
                <button @click="mModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-6 overflow-y-auto flex-1">
                
                {{-- Auto-fill from general measurements badge --}}
                <div x-show="mLoadedFromGeneral" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                    <span class="flex items-center gap-2 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Auto-filled from Customer's General Body Profile
                    </span>
                    <span class="text-[11px] text-emerald-600" x-text="'Saved ' + (mSavedDate || 'Profile')"></span>
                </div>

                {{-- Upper & Lower Body Measurements — Side by Side --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Upper Body Measurements Section --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-base">👕</span>
                                <h4 class="text-sm font-bold text-slate-800">Upper Body</h4>
                            </div>
                            <button type="button" @click="promptAddUpperField()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-dashed border-indigo-300 hover:border-indigo-500 bg-indigo-50/50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                title="Add a new custom measurement field">
                                <span class="font-extrabold leading-none">+</span>
                                <span>Add Field</span>
                            </button>
                        </div>

                        {{-- Interactive Label Buttons Bar --}}
                        <div class="space-y-1.5">
                            <button type="button" @click="upperLabelsOpen = !upperLabelsOpen"
                                class="w-full flex items-center justify-between text-left group">
                                <p class="text-xs font-semibold text-slate-500 group-hover:text-indigo-600 transition-colors">
                                    Click label buttons to open input fields <span class="text-slate-400 font-normal">(e.g. 32, 34, 36)</span>:
                                </p>
                                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                    <span class="text-[11px] text-slate-400" x-text="mActiveUpperKeys.length + ' selected'"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition-all duration-200"
                                        :class="upperLabelsOpen ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </div>
                            </button>

                            <div x-show="upperLabelsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1">
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <template x-for="f in mUpperFields" :key="'mbtn_u_'+f.key">
                                        <button type="button"
                                            @click="toggleUpperField(f.key)"
                                            :class="{
                                                'bg-indigo-600 text-white border-indigo-600 shadow-sm ring-2 ring-indigo-200': mActiveUpperKeys.includes(f.key),
                                                'bg-indigo-50/80 text-indigo-700 border-indigo-200 hover:bg-indigo-100': !mActiveUpperKeys.includes(f.key) && parseValues(mValues[f.key]).length > 0,
                                                'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40': !mActiveUpperKeys.includes(f.key) && parseValues(mValues[f.key]).length === 0
                                            }"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-semibold tracking-wide transition-all cursor-pointer select-none shadow-xs hover:shadow">
                                            <span class="text-[12px] font-black leading-none" x-text="mActiveUpperKeys.includes(f.key) ? '✓' : '+'"></span>
                                            <span x-text="f.label"></span>
                                            <span x-show="parseValues(mValues[f.key]).length > 0"
                                                :class="mActiveUpperKeys.includes(f.key) ? 'bg-white/25 text-white' : 'bg-indigo-200/90 text-indigo-900'"
                                                class="ml-0.5 px-1 py-0.5 rounded-full text-[10px] font-bold"
                                                x-text="parseValues(mValues[f.key]).length"></span>
                                            <span @click.stop="confirmRemoveUpperField(f)"
                                                class="ml-0.5 opacity-50 hover:opacity-100 hover:text-red-500 font-bold text-xs transition"
                                                :title="'Remove ' + f.label + ' button'">&times;</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Dynamic Active Input Fields Grid (2-per-row) --}}
                        <div x-show="mActiveUpperKeys.length > 0" class="grid grid-cols-2 gap-2 pt-1">
                            <template x-for="f in activeUpperFieldsList" :key="'minp_u_'+f.key">
                                <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200/90 rounded-xl p-1.5 transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100 shadow-2xs flex items-center justify-between gap-1" @click.outside="mChipsOpen[f.key] = false">
                                    <div class="flex items-center gap-1 min-w-0">
                                        {{-- Label --}}
                                        <label :for="'mf_'+f.key" class="text-[11px] font-bold text-slate-800 truncate flex-shrink-0 max-w-[55px]" x-text="f.label"></label>
                                        
                                        {{-- Input and Separate Dropdown Button --}}
                                        <div class="flex items-center gap-1 relative" @click.outside="mChipsOpen[f.key] = false">
                                            
                                            {{-- Dropdown Toggle Button (Hide if no options) --}}
                                            <button type="button" tabindex="-1"
                                                x-show="getFieldOptions(f.key).length > 0"
                                                @click="mChipsOpen[f.key] = !mChipsOpen[f.key]"
                                                class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-slate-300 rounded-md text-slate-500 hover:text-indigo-600 hover:border-indigo-400 transition cursor-pointer shadow-2xs">
                                                <svg class="w-3 h-3 transition-transform duration-200"
                                                    :class="mChipsOpen[f.key] ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                </svg>
                                            </button>

                                            {{-- Input Field (Hidden initially until size selected or custom clicked) --}}
                                            <div class="relative w-16" x-show="mInputShow[f.key] || (mValues[f.key] && String(mValues[f.key]).trim() !== '')">
                                                <input type="text" :id="'mf_'+f.key" x-model="mValues[f.key]"
                                                    :placeholder="getFieldOptions(f.key).length > 0 ? getFieldOptions(f.key)[0] : ''"
                                                    class="w-full pl-1.5 pr-4 py-0.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition cursor-text shadow-2xs">
                                                <span class="absolute inset-y-0 right-0 pr-1 flex items-center pointer-events-none text-[9px] text-slate-400 font-medium">in</span>
                                            </div>
                                            
                                            {{-- Floating Vertical Dropdown (Column) --}}
                                            <div x-show="mChipsOpen[f.key]" 
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0 translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-2"
                                                class="absolute left-0 bottom-full mb-1 z-50 min-w-[75px] bg-white border border-slate-200 rounded-lg shadow-lg max-h-40 overflow-y-auto py-1">
                                                <template x-if="getFieldOptions(f.key).length === 0">
                                                    <div class="px-2 py-1.5 text-center">
                                                        <p class="text-[10px] text-slate-400 mb-1">No sizes</p>
                                                        <button type="button" @click="mInputShow[f.key] = true; mChipsOpen[f.key] = false"
                                                            class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[9px] font-bold hover:bg-indigo-100 transition">
                                                            + Custom
                                                        </button>
                                                    </div>
                                                </template>
                                                <template x-for="(val, idx) in getFieldOptions(f.key)" :key="idx">
                                                    <button type="button" @click="selectSingleMeasurement(f.key, val); mChipsOpen[f.key] = false"
                                                        :class="isOptionSelected(f.key, val) ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                                                        class="w-full text-left px-3 py-1.5 text-xs transition cursor-pointer flex justify-between items-center">
                                                        <span x-text="val"></span>
                                                        <span x-show="isOptionSelected(f.key, val)" class="text-indigo-500 font-bold">✓</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Close Button --}}
                                    <button type="button" @click="toggleUpperField(f.key)"
                                        class="text-slate-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition flex-shrink-0"
                                        title="Close this field">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Empty state --}}
                        <div x-show="mActiveUpperKeys.length === 0" class="text-center py-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <span class="text-lg block mb-1">👕</span>
                            <p class="text-xs font-medium text-slate-600">No upper body fields selected yet.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click a label button above to add values.</p>
                        </div>
                    </div>

                    {{-- Lower Body Measurements Section --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs p-4 space-y-3">
                        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="text-base">👖</span>
                                <h4 class="text-sm font-bold text-slate-800">Lower Body</h4>
                            </div>
                            <button type="button" @click="promptAddLowerField()"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-dashed border-emerald-300 hover:border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-all cursor-pointer shadow-2xs"
                                title="Add a new custom measurement field">
                                <span class="font-extrabold leading-none">+</span>
                                <span>Add Field</span>
                            </button>
                        </div>

                        {{-- Interactive Label Buttons Bar --}}
                        <div class="space-y-1.5">
                            <button type="button" @click="lowerLabelsOpen = !lowerLabelsOpen"
                                class="w-full flex items-center justify-between text-left group">
                                <p class="text-xs font-semibold text-slate-500 group-hover:text-emerald-600 transition-colors">
                                    Click label buttons to open input fields <span class="text-slate-400 font-normal">(e.g. 38, 40)</span>:
                                </p>
                                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                    <span class="text-[11px] text-slate-400" x-text="mActiveLowerKeys.length + ' selected'"></span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-emerald-500 transition-all duration-200"
                                        :class="lowerLabelsOpen ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                </div>
                            </button>

                            <div x-show="lowerLabelsOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1">
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <template x-for="f in mLowerFields" :key="'mbtn_l_'+f.key">
                                        <button type="button"
                                            @click="toggleLowerField(f.key)"
                                            :class="{
                                                'bg-emerald-600 text-white border-emerald-600 shadow-sm ring-2 ring-emerald-200': mActiveLowerKeys.includes(f.key),
                                                'bg-emerald-50/80 text-emerald-700 border-emerald-200 hover:bg-emerald-100': !mActiveLowerKeys.includes(f.key) && parseValues(mValues[f.key]).length > 0,
                                                'bg-white text-slate-700 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40': !mActiveLowerKeys.includes(f.key) && parseValues(mValues[f.key]).length === 0
                                            }"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-semibold tracking-wide transition-all cursor-pointer select-none shadow-xs hover:shadow">
                                            <span class="text-[12px] font-black leading-none" x-text="mActiveLowerKeys.includes(f.key) ? '✓' : '+'"></span>
                                            <span x-text="f.label"></span>
                                            <span x-show="parseValues(mValues[f.key]).length > 0"
                                                :class="mActiveLowerKeys.includes(f.key) ? 'bg-white/25 text-white' : 'bg-emerald-200/90 text-emerald-900'"
                                                class="ml-0.5 px-1 py-0.5 rounded-full text-[10px] font-bold"
                                                x-text="parseValues(mValues[f.key]).length"></span>
                                            <span @click.stop="confirmRemoveLowerField(f)"
                                                class="ml-0.5 opacity-50 hover:opacity-100 hover:text-red-500 font-bold text-xs transition"
                                                :title="'Remove ' + f.label + ' button'">&times;</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Dynamic Active Input Fields Grid (2-per-row) --}}
                        <div x-show="mActiveLowerKeys.length > 0" class="grid grid-cols-2 gap-2 pt-1">
                            <template x-for="f in activeLowerFieldsList" :key="'minp_l_'+f.key">
                                <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200/90 rounded-xl p-1.5 transition focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-100 shadow-2xs flex items-center justify-between gap-1" @click.outside="mChipsOpen[f.key] = false">
                                    <div class="flex items-center gap-1 min-w-0">
                                        {{-- Label --}}
                                        <label :for="'mf_'+f.key" class="text-[11px] font-bold text-slate-800 truncate flex-shrink-0 max-w-[55px]" x-text="f.label"></label>
                                        
                                        {{-- Input and Separate Dropdown Button --}}
                                        <div class="flex items-center gap-1 relative" @click.outside="mChipsOpen[f.key] = false">
                                            
                                            {{-- Dropdown Toggle Button (Hide if no options) --}}
                                            <button type="button" tabindex="-1"
                                                x-show="getFieldOptions(f.key).length > 0"
                                                @click="mChipsOpen[f.key] = !mChipsOpen[f.key]"
                                                class="flex-shrink-0 w-6 h-6 flex items-center justify-center bg-white border border-slate-300 rounded-md text-slate-500 hover:text-emerald-600 hover:border-emerald-400 transition cursor-pointer shadow-2xs">
                                                <svg class="w-3 h-3 transition-transform duration-200"
                                                    :class="mChipsOpen[f.key] ? 'rotate-180' : ''"
                                                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                </svg>
                                            </button>

                                            {{-- Input Field (Hidden initially until size selected or custom clicked) --}}
                                            <div class="relative w-16" x-show="mInputShow[f.key] || (mValues[f.key] && String(mValues[f.key]).trim() !== '')">
                                                <input type="text" :id="'mf_'+f.key" x-model="mValues[f.key]"
                                                    :placeholder="getFieldOptions(f.key).length > 0 ? getFieldOptions(f.key)[0] : ''"
                                                    class="w-full pl-1.5 pr-4 py-0.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition cursor-text shadow-2xs">
                                                <span class="absolute inset-y-0 right-0 pr-1 flex items-center pointer-events-none text-[9px] text-slate-400 font-medium">in</span>
                                            </div>
                                            
                                            {{-- Floating Vertical Dropdown (Column) --}}
                                            <div x-show="mChipsOpen[f.key]" 
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0 translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 translate-y-2"
                                                class="absolute left-0 bottom-full mb-1 z-50 min-w-[75px] bg-white border border-slate-200 rounded-lg shadow-lg max-h-40 overflow-y-auto py-1">
                                                <template x-if="getFieldOptions(f.key).length === 0">
                                                    <div class="px-2 py-1.5 text-center">
                                                        <p class="text-[10px] text-slate-400 mb-1">No sizes</p>
                                                        <button type="button" @click="mInputShow[f.key] = true; mChipsOpen[f.key] = false"
                                                            class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-bold hover:bg-emerald-100 transition">
                                                            + Custom
                                                        </button>
                                                    </div>
                                                </template>
                                                <template x-for="(val, idx) in getFieldOptions(f.key)" :key="idx">
                                                    <button type="button" @click="selectSingleMeasurement(f.key, val); mChipsOpen[f.key] = false"
                                                        :class="isOptionSelected(f.key, val) ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 hover:bg-slate-50'"
                                                        class="w-full text-left px-3 py-1.5 text-xs transition cursor-pointer flex justify-between items-center">
                                                        <span x-text="val"></span>
                                                        <span x-show="isOptionSelected(f.key, val)" class="text-emerald-500 font-bold">✓</span>
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Close Button --}}
                                    <button type="button" @click="toggleLowerField(f.key)"
                                        class="text-slate-400 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition flex-shrink-0"
                                        title="Close this field">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Empty state --}}
                        <div x-show="mActiveLowerKeys.length === 0" class="text-center py-4 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <span class="text-lg block mb-1">👖</span>
                            <p class="text-xs font-medium text-slate-600">No lower body fields selected yet.</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Click a label button above to add values.</p>
                        </div>
                    </div>

                </div>{{-- end grid --}}

                <p x-show="mToast" x-text="mToast" class="text-sm font-semibold text-red-500"></p>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-slate-50 flex-shrink-0">
                <button type="button" @click="mValues = {}; mActiveUpperKeys = []; mActiveLowerKeys = [];" class="text-xs font-semibold text-slate-400 hover:text-red-500 transition">
                    Clear All
                </button>
                <div class="flex items-center gap-3">
                    <button type="button" @click="mModal = false"
                        class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition shadow-xs">
                        Cancel
                    </button>
                    <button type="button" @click="saveMeasurements()" :disabled="mSaving"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-500/20 transition disabled:opacity-50">
                        <svg x-show="!mSaving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <span x-text="mSaving ? 'Saving...' : 'Apply Measurements'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= QUICK CUSTOMER MODAL ================= --}}
    <template x-if="showQuickCustomer">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="showQuickCustomer = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Quick Add Customer</h3>
                    <button @click="showQuickCustomer = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="qc.name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Full name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" x-model="qc.phone" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="03XX-XXXXXXX">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Gender</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer"><input type="radio" value="male" x-model="qc.gender" class="text-indigo-600"> Male</label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer"><input type="radio" value="female" x-model="qc.gender" class="text-indigo-600"> Female</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Address</label>
                        <input type="text" x-model="qc.address" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional address">
                    </div>
                </div>
                <p x-show="qcError" x-text="qcError" class="text-xs text-red-500"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button @click="showQuickCustomer = false" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">Cancel</button>
                    <button @click="saveQuickCustomer()" :disabled="qcSaving" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl disabled:opacity-50">
                        <span x-text="qcSaving ? 'Saving...' : 'Save & Select'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ================= QUICK MEMBER MODAL ================= --}}
    <template x-if="showQuickMember">
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-xs" @click="showQuickMember = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-800">Add Family Member</h3>
                    <button @click="showQuickMember = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Member Name <span class="text-red-500">*</span></label>
                        <input type="text" x-model="qm.name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Ali, Hamza">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Relation <span class="text-red-500">*</span></label>
                        <select x-model="qm.relation" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500">
                            <option value="">Select relation</option>
                            <option value="son">Son</option>
                            <option value="brother">Brother</option>
                            <option value="father">Father</option>
                            <option value="daughter">Daughter</option>
                            <option value="sister">Sister</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Gender</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer"><input type="radio" value="male" x-model="qm.gender" class="text-indigo-600"> Male</label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer"><input type="radio" value="female" x-model="qm.gender" class="text-indigo-600"> Female</label>
                        </div>
                    </div>
                </div>
                <p x-show="qmError" x-text="qmError" class="text-xs text-red-500"></p>
                <div class="flex justify-end gap-2 pt-2">
                    <button @click="showQuickMember = false" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">Cancel</button>
                    <button @click="saveQuickMember()" :disabled="qmSaving" class="px-5 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl disabled:opacity-50">
                        <span x-text="qmSaving ? 'Saving...' : 'Save Member'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]').content;
window.__editOrder = @json($editOrder);

const DEFAULT_UPPER_FIELDS = [
    { key: 'kameez_length', label: 'Length', urdu: 'لمبائی' },
    { key: 'chest', label: 'Chest', urdu: 'چھاتی' },
    { key: 'waist_upper', label: 'Waist', urdu: 'کمر' },
    { key: 'shoulder', label: 'Shoulder', urdu: 'تیرا' },
    { key: 'sleeves', label: 'Sleeves', urdu: 'بازو' },
    { key: 'collar', label: 'Collar / Neck', urdu: 'کالر / گلا' },
    { key: 'daman', label: 'Daman / Ghera', urdu: 'دامن / گھیرا' },
    { key: 'cross_back', label: 'Cross Back', urdu: 'کراس بیک' },
    { key: 'bicep', label: 'Bicep', urdu: 'مسل' },
    { key: 'wrist', label: 'Cuff / Wrist', urdu: 'کف' },
];

const DEFAULT_LOWER_FIELDS = [
    { key: 'shalwar_length', label: 'Length', urdu: 'شلوار / پینٹ لمبائی' },
    { key: 'waist_lower', label: 'Waist', urdu: 'کمر' },
    { key: 'hip', label: 'Hip', urdu: 'ہپ / سیٹ' },
    { key: 'inseam', label: 'Inseam', urdu: 'اندر کی لمبائی' },
    { key: 'paincha', label: 'Paincha / Bottom', urdu: 'پانچہ / موری' },
    { key: 'thigh', label: 'Thigh', urdu: 'ران' },
    { key: 'asan', label: 'Asan / Fly', urdu: 'آسن / فلائی' },
];

function getStoredMeasurementFields(storageKey, defaultList) {
    try {
        const stored = localStorage.getItem(storageKey);
        if (stored !== null) {
            const parsed = JSON.parse(stored);
            if (Array.isArray(parsed)) {
                return parsed;
            }
        }
    } catch (e) {
        console.warn('Error reading ' + storageKey + ' from localStorage', e);
    }
    return defaultList.map(f => ({ ...f }));
}

function orderWizard() {
    const eo = window.__editOrder || null;
    return {
        editMode: !!eo,
        editOrderId: eo ? eo.id : null,
        searchQuery: '',
        searchResults: { customers: [], members: [] },
        searchLoading: false,
        showDropdown: false,
        selectedCustomer: null,
        ledger: null,
        selectedMemberId: null,
        soloMember: false,
        services: [],
        servicesLoading: false,
        serviceSearch: '',
        servicePage: 0,
        servicePageSize: 6,
        selectedServices: [],
        serviceMeasurements: {},
        servicePrices: {},
        serviceTiers: {},
        serviceQty: {},
        servicePrev: {},
        serviceNotes: {},
        loadingServiceIds: [],
        submitting: false,
        today: new Date().toISOString().split('T')[0],
        calcDefaultDueDate(days = 7) {
            const d = new Date();
            d.setDate(d.getDate() + (parseInt(days) || 7));
            return d.toISOString().split('T')[0];
        },
        form: { customer_id:'', member_id:'', paid_amount:'', account_id:'', due_date: (function() { const d = new Date(); d.setDate(d.getDate() + 7); return d.toISOString().split('T')[0]; })(), notes:'' },
        errors: {},
        fieldErrors: {},

        // Quick customer modal
        showQuickCustomer: false,
        qcSaving: false,
        qcError: '',
        qc: { name: '', phone: '', gender: 'male', address: '' },

        // Quick member modal
        showQuickMember: false,
        qmSaving: false,
        qmError: '',
        qm: { name: '', relation: '', gender: 'male' },

        mModal: false,
        mSaving: false,
        mService: null,
        mValues: {},
        mInputShow: {},
        mOptionValues: {},
        mCustomerStyles: null,
        mLoadedFromGeneral: false,
        mSavedDate: '',
        mFieldErrors: {},
        mToast: '',

        // Upper & Lower Body persistent fields and active keys
        mUpperFields: getStoredMeasurementFields('tailor_upper_fields', DEFAULT_UPPER_FIELDS),
        mLowerFields: getStoredMeasurementFields('tailor_lower_fields', DEFAULT_LOWER_FIELDS),
        mActiveUpperKeys: [],
        mActiveLowerKeys: [],
        upperLabelsOpen: false,
        lowerLabelsOpen: false,
        mChipsOpen: {},

        saveMFieldsToStorage() {
            try {
                localStorage.setItem('tailor_upper_fields', JSON.stringify(this.mUpperFields));
                localStorage.setItem('tailor_lower_fields', JSON.stringify(this.mLowerFields));
            } catch (e) {
                console.error('Failed to save measurement fields to localStorage', e);
            }
        },

        loadMFieldsFromStorage() {
            this.mUpperFields = getStoredMeasurementFields('tailor_upper_fields', DEFAULT_UPPER_FIELDS);
            this.mLowerFields = getStoredMeasurementFields('tailor_lower_fields', DEFAULT_LOWER_FIELDS);
        },

        parseValues(val) {
            if (val === undefined || val === null) return [];
            if (Array.isArray(val)) {
                return val.map(v => String(v).trim()).filter(v => v !== '');
            }
            return String(val).split(',').map(v => v.trim()).filter(v => v !== '');
        },

        getFieldOptions(key) {
            return this.mOptionValues[key] || [];
        },

        isOptionSelected(key, val) {
            const current = String(this.mValues[key] || '').trim();
            return current === String(val).trim();
        },

        selectSingleMeasurement(key, val) {
            this.mValues[key] = String(val).trim();
            this.mInputShow[key] = true;
        },

        // Searchable Service Dropdown in Table
        activeServiceDropdown: null,
        serviceFilterQuery: {},
        dropdownRect: null,

        // Price Tier (Basic/Standard/Premium) Dropdown
        activePriceDropdown: null,
        priceDropRect: null,

        _priceScrollHandler: null,

        repositionPriceDropdown() {
            const id = this.activePriceDropdown;
            if (!id) return;
            const btn = document.getElementById('tier_btn_' + id);
            if (btn) {
                const r = btn.getBoundingClientRect();
                this.priceDropRect = { id, top: r.bottom + 6, left: r.left, width: Math.max(r.width, 160) };
            }
        },

        get priceDropdownStyle() {
            const r = this.priceDropRect && this.priceDropRect.id === this.activePriceDropdown ? this.priceDropRect : null;
            return r
                ? { position: 'fixed', top: r.top + 'px', left: r.left + 'px', width: r.width + 'px', zIndex: 9999 }
                : { display: 'none' };
        },

        openPriceDropdown(svcId) {
            this.activePriceDropdown = svcId;
            if (this._priceScrollHandler) {
                window.removeEventListener('scroll', this._priceScrollHandler, true);
                window.removeEventListener('resize', this._priceScrollHandler);
            }
            this._priceScrollHandler = () => this.repositionPriceDropdown();
            window.addEventListener('scroll', this._priceScrollHandler, true);
            window.addEventListener('resize', this._priceScrollHandler);
            this.$nextTick(() => {
                const btn = document.getElementById('tier_btn_' + svcId);
                if (btn) {
                    const r = btn.getBoundingClientRect();
                    this.priceDropRect = { id: svcId, top: r.bottom + 6, left: r.left, width: Math.max(r.width, 160) };
                }
            });
        },

        closePriceDropdown(svcId) {
            if (this._priceScrollHandler) {
                window.removeEventListener('scroll', this._priceScrollHandler, true);
                window.removeEventListener('resize', this._priceScrollHandler);
                this._priceScrollHandler = null;
            }
            if (this.activePriceDropdown === svcId) {
                this.activePriceDropdown = null;
                this.priceDropRect = null;
            }
        },

        pickPriceTierFromDropdown(svcId, tier) {
            const svc = this.getSvcById(svcId);
            if (svc) {
                this.setServiceTier(svc, tier);
            }
            this.closePriceDropdown(svcId);
        },

        getTierLabel(t) {
            return t === 'basic' ? 'Basic' : t === 'premium' ? 'Premium' : 'Standard';
        },

        _svcScrollHandler: null,

        _calcSvcDropdownRect(svcId) {
            const inp = document.getElementById('svc_input_' + svcId);
            if (!inp) return null;
            const r = inp.getBoundingClientRect();
            // Always open below the input field
            return { id: svcId, top: r.bottom + 6, left: r.left, width: Math.max(r.width, 240) };
        },

        repositionDropdown() {
            const id = this.activeServiceDropdown;
            if (!id) return;
            const rect = this._calcSvcDropdownRect(id);
            if (rect) this.dropdownRect = rect;
        },

        get dropdownStyle() {
            const r = this.dropdownRect && this.dropdownRect.id === this.activeServiceDropdown ? this.dropdownRect : null;
            return r
                ? { position: 'fixed', top: r.top + 'px', left: r.left + 'px', width: r.width + 'px', zIndex: 9999 }
                : { display: 'none' };
        },

        openServiceDropdown(svcId, svc) {
            // Close any other open dropdown first
            if (this.activeServiceDropdown && this.activeServiceDropdown !== svcId) {
                this._closeSvcDropdownInternal();
            }
            this.activeServiceDropdown = svcId;
            this.serviceFilterQuery[svcId] = '';

            // Clean up old handler
            if (this._svcScrollHandler) {
                window.removeEventListener('scroll', this._svcScrollHandler, true);
                window.removeEventListener('resize', this._svcScrollHandler);
            }
            // Store bound arrow fn so it always has correct `this`
            this._svcScrollHandler = () => this.repositionDropdown();
            window.addEventListener('scroll', this._svcScrollHandler, true);
            window.addEventListener('resize', this._svcScrollHandler);

            this.$nextTick(() => {
                const rect = this._calcSvcDropdownRect(svcId);
                if (rect) {
                    this.dropdownRect = rect;
                    const inp = document.getElementById('svc_input_' + svcId);
                    if (inp) inp.select();
                }
            });
        },

        _closeSvcDropdownInternal() {
            if (this._svcScrollHandler) {
                window.removeEventListener('scroll', this._svcScrollHandler, true);
                window.removeEventListener('resize', this._svcScrollHandler);
                this._svcScrollHandler = null;
            }
            this.activeServiceDropdown = null;
            this.dropdownRect = null;
        },

        closeServiceDropdown(svcId) {
            if (this.activeServiceDropdown === svcId) {
                this._closeSvcDropdownInternal();
                delete this.serviceFilterQuery[svcId];
            }
        },

        getFilteredServicesFor(svcId) {
            const q = (this.serviceFilterQuery[svcId] || '').trim().toLowerCase();
            // Dedup by name+days to prevent visually identical entries
            const seen = new Set();
            return this.services.filter(s => {
                // Deduplicate by name+days key (handles DB duplicates with different IDs)
                const key = (s.name || '') + '|' + (s.days ?? '');
                if (seen.has(key)) return false;
                seen.add(key);

                if (!q) return true;
                const name = (s.name || '').toLowerCase();
                const days = s.days ? String(s.days) : '';
                return name.includes(q) || days.includes(q);
            });
        },

        async pickServiceFromDropdown(oldId, newId) {
            this.activeServiceDropdown = null;
            await this.changeService(oldId, newId);
        },

        get activeUpperFieldsList() {
            return this.mUpperFields.filter(f => this.mActiveUpperKeys.includes(f.key));
        },

        get activeLowerFieldsList() {
            return this.mLowerFields.filter(f => this.mActiveLowerKeys.includes(f.key));
        },

        toggleUpperField(key) {
            const idx = this.mActiveUpperKeys.indexOf(key);
            if (idx > -1) {
                this.mActiveUpperKeys.splice(idx, 1);
            } else {
                this.mActiveUpperKeys.push(key);
                this.mInputShow[key] = true;
                const opts = this.getFieldOptions(key);
                if (opts.length > 0 && (!this.mValues[key] || String(this.mValues[key]).trim() === '')) {
                    // DO NOT auto-assign value, just let placeholder show it
                }
            }
        },

        toggleLowerField(key) {
            const idx = this.mActiveLowerKeys.indexOf(key);
            if (idx > -1) {
                this.mActiveLowerKeys.splice(idx, 1);
            } else {
                this.mActiveLowerKeys.push(key);
                this.mInputShow[key] = true;
                const opts = this.getFieldOptions(key);
                if (opts.length > 0 && (!this.mValues[key] || String(this.mValues[key]).trim() === '')) {
                    // DO NOT auto-assign value
                }
            }
        },

        showAllUpper() {
            this.mActiveUpperKeys = this.mUpperFields.map(f => f.key);
        },

        hideAllUpper() {
            this.mActiveUpperKeys = [];
        },

        showAllLower() {
            this.mActiveLowerKeys = this.mLowerFields.map(f => f.key);
        },

        hideAllLower() {
            this.mActiveLowerKeys = [];
        },

        async promptAddUpperField() {
            const { value: formValues } = await Swal.fire({
                title: 'Add New Upper Body Field',
                html: `
                    <div class="text-left space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Field Name *</label>
                            <input id="swal_field_label" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="e.g. Armhole, Pocket Width...">
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '+ Add Field',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl'
                },
                didOpen: () => {
                    const inp = document.getElementById('swal_field_label');
                    if (inp) inp.focus();
                },
                preConfirm: () => {
                    const label = (document.getElementById('swal_field_label').value || '').trim();
                    if (!label) {
                        Swal.showValidationMessage('Field name is required');
                        return false;
                    }
                    return { label, urdu: label };
                }
            });

            if (formValues) {
                const key = 'custom_u_' + formValues.label.toLowerCase().replace(/[^a-z0-9_]/g, '_') + '_' + Date.now().toString().slice(-4);
                const newField = {
                    key: key,
                    label: formValues.label,
                    urdu: formValues.urdu,
                    isCustom: true
                };
                this.mUpperFields.push(newField);
                this.saveMFieldsToStorage();
                if (!this.mActiveUpperKeys.includes(key)) {
                    this.mActiveUpperKeys.push(key);
                }
                this.$nextTick(() => {
                    const el = document.getElementById('mf_' + key);
                    if (el) el.focus();
                });
            }
        },

        async confirmRemoveUpperField(field) {
            const hasVal = this.parseValues(this.mValues[field.key]).length > 0;
            const res = await Swal.fire({
                title: 'Remove "' + field.label + '"?',
                text: hasVal
                    ? 'This button has entered measurements which will also be cleared.'
                    : 'Are you sure you want to remove this measurement button?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                customClass: { popup: 'rounded-2xl shadow-2xl' }
            });
            if (res.isConfirmed) {
                this.removeUpperField(field.key);
            }
        },

        removeUpperField(key) {
            this.mUpperFields = this.mUpperFields.filter(f => f.key !== key);
            this.mActiveUpperKeys = this.mActiveUpperKeys.filter(k => k !== key);
            delete this.mValues[key];
            this.saveMFieldsToStorage();
        },

        async resetUpperFields() {
            const res = await Swal.fire({
                title: 'Reset Upper Body Buttons?',
                text: 'This will restore all default buttons (Length, Chest, Waist, etc.).',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reset',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                customClass: { popup: 'rounded-2xl shadow-2xl' }
            });
            if (res.isConfirmed) {
                this.mUpperFields = DEFAULT_UPPER_FIELDS.map(f => ({ ...f }));
                this.saveMFieldsToStorage();
            }
        },

        async promptAddLowerField() {
            const { value: formValues } = await Swal.fire({
                title: 'Add New Lower Body Field',
                html: `
                    <div class="text-left space-y-3 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Field Name *</label>
                            <input id="swal_lfield_label" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" placeholder="e.g. Belt, Pocket, Calf...">
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: '+ Add Field',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                customClass: {
                    popup: 'rounded-2xl shadow-2xl'
                },
                didOpen: () => {
                    const inp = document.getElementById('swal_lfield_label');
                    if (inp) inp.focus();
                },
                preConfirm: () => {
                    const label = (document.getElementById('swal_lfield_label').value || '').trim();
                    if (!label) {
                        Swal.showValidationMessage('Field name is required');
                        return false;
                    }
                    return { label, urdu: label };
                }
            });

            if (formValues) {
                const key = 'custom_l_' + formValues.label.toLowerCase().replace(/[^a-z0-9_]/g, '_') + '_' + Date.now().toString().slice(-4);
                const newField = {
                    key: key,
                    label: formValues.label,
                    urdu: formValues.urdu,
                    isCustom: true
                };
                this.mLowerFields.push(newField);
                this.saveMFieldsToStorage();
                if (!this.mActiveLowerKeys.includes(key)) {
                    this.mActiveLowerKeys.push(key);
                }
                this.$nextTick(() => {
                    const el = document.getElementById('mf_' + key);
                    if (el) el.focus();
                });
            }
        },

        async confirmRemoveLowerField(field) {
            const hasVal = this.parseValues(this.mValues[field.key]).length > 0;
            const res = await Swal.fire({
                title: 'Remove "' + field.label + '"?',
                text: hasVal
                    ? 'This button has entered measurements which will also be cleared.'
                    : 'Are you sure you want to remove this measurement button?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Remove',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                customClass: { popup: 'rounded-2xl shadow-2xl' }
            });
            if (res.isConfirmed) {
                this.removeLowerField(field.key);
            }
        },

        removeLowerField(key) {
            this.mLowerFields = this.mLowerFields.filter(f => f.key !== key);
            this.mActiveLowerKeys = this.mActiveLowerKeys.filter(k => k !== key);
            delete this.mValues[key];
            this.saveMFieldsToStorage();
        },

        async resetLowerFields() {
            const res = await Swal.fire({
                title: 'Reset Lower Body Buttons?',
                text: 'This will restore all default buttons (Length, Waist, Hip, etc.).',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reset',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                customClass: { popup: 'rounded-2xl shadow-2xl' }
            });
            if (res.isConfirmed) {
                this.mLowerFields = DEFAULT_LOWER_FIELDS.map(f => ({ ...f }));
                this.saveMFieldsToStorage();
            }
        },

        async init() {
            if (this.editMode) {
                await this.initEditOrder();
            }
        },

        async initEditOrder() {
            const eo = window.__editOrder;
            if (!eo || !eo.customer) return;
            const customer = eo.customer;
            this.selectedCustomer = {
                id: customer.id,
                name: customer.name,
                phone: customer.phone,
                gender: customer.gender,
                members: customer.members || [],
            };
            this.form.customer_id = customer.id;
            await this.loadLedger(this.selectedCustomer);

            if (eo.member_id) {
                this.pickMember({ id: eo.member_id, name: eo.member?.name || '', relation: eo.member?.relation || '', gender: eo.member?.gender || '' });
            } else {
                this.pickMember({ id: '__self__', name: customer.name, relation: '', gender: customer.gender, isSelf: true });
            }
            this.searchQuery = '';

            await this.loadMemberServices();

            // Clear any blank auto-added service rows
            this.selectedServices = [];

            const ordersToLoad = (eo.sibling_orders && Array.isArray(eo.sibling_orders) && eo.sibling_orders.length > 0)
                ? eo.sibling_orders
                : [eo];

            for (const ord of ordersToLoad) {
                let svc = this.services.find(s => s.id == ord.service_id);
                if (!svc && ord.service) {
                    svc = ord.service;
                    if (!this.services.some(s => s.id == svc.id)) {
                        this.services.push(svc);
                    }
                }
                if (svc) {
                    if (!this.selectedServices.some(s => s.id == svc.id)) {
                        this.selectedServices.push(svc);
                    }
                    this.servicePrices[svc.id] = ord.price;
                    this.serviceTiers[svc.id] = this.detectTierFromPrice(svc, ord.price);
                    this.serviceQty[svc.id] = ord.quantity || 1;
                    this.serviceNotes[svc.id] = ord.notes || '';
                    let measurements = {};
                    if (typeof ord.measurements === 'object' && ord.measurements !== null) {
                        measurements = ord.measurements;
                    } else if (typeof ord.measurements === 'string' && ord.measurements.trim() !== '') {
                        try { measurements = JSON.parse(ord.measurements); } catch(e) {}
                    }
                    this.serviceMeasurements[svc.id] = measurements;
                }
            }

            this.form.paid_amount = eo.paid_amount || '';
            this.form.due_date = eo.due_date ? eo.due_date.substring(0, 10) : '';
            this.form.notes = eo.notes || '';
        },

        get customers() {
            return Array.isArray(this.searchResults.customers) ? this.searchResults.customers : [];
        },

        get members() {
            return Array.isArray(this.searchResults.members) ? this.searchResults.members : [];
        },

        get visibleServices() {
            const q = (this.serviceSearch || '').trim().toLowerCase();
            if (!q) return this.services;
            return (this.services || []).filter(s => (s.name || '').toLowerCase().includes(q));
        },

        get filteredServices() {
            return this.visibleServices;
        },

        get visiblePages() {
            const arr = this.visibleServices;
            if (!arr.length) return [];
            return Array.from({ length: Math.ceil(arr.length / this.servicePageSize) }, (_, i) => arr.slice(i * this.servicePageSize, i * this.servicePageSize + this.servicePageSize));
        },

        get currentPageServices() {
            const pages = this.visiblePages;
            if (!pages.length) return [];
            const idx = Math.min(this.servicePage, pages.length - 1);
            return pages[idx];
        },

        get memberOptions() {
            if (!this.selectedCustomer) return [];
            const all = [
                { id: '__self__', name: this.selectedCustomer.name, relation: '', gender: this.selectedCustomer.gender, isSelf: true },
                ...(this.selectedCustomer.members || []).map(m => ({ ...m, isSelf: false }))
            ];
            if (this.soloMember && this.selectedMemberId !== null) {
                return all.filter(m => m.id === this.selectedMemberId);
            }
            return all;
        },

        get totalPrice() {
            return this.selectedServices.reduce((sum, svc) => {
                const qty = parseInt(this.serviceQty[svc.id]) || 1;
                return sum + ((parseFloat(this.servicePrices[svc.id]) || 0) * qty);
            }, 0);
        },

        get remainingDue() {
            return Math.max(0, this.totalPrice - (parseFloat(this.form.paid_amount) || 0));
        },

        // Pricing tier helper methods
        getTierPrice(svc, tier = 'standard') {
            if (!svc) return 0;
            const tiers = svc.pricing_tiers || {};
            if (tiers[tier] !== undefined && tiers[tier] !== null && tiers[tier] !== '') {
                return parseFloat(tiers[tier]) || 0;
            }
            const base = parseFloat(svc.price) || 0;
            if (tier === 'basic') return Math.round(base * 0.75);
            if (tier === 'premium') return Math.round(base * 1.6);
            return base;
        },

        detectTierFromPrice(svc, price) {
            if (!svc) return 'standard';
            const numPrice = parseFloat(price) || 0;
            const tiers = svc.pricing_tiers || {};
            if (tiers.basic !== undefined && parseFloat(tiers.basic) === numPrice) return 'basic';
            if (tiers.premium !== undefined && parseFloat(tiers.premium) === numPrice) return 'premium';
            if (tiers.standard !== undefined && parseFloat(tiers.standard) === numPrice) return 'standard';
            return 'standard';
        },

        getServiceTier(serviceId) {
            return this.serviceTiers[serviceId] || 'standard';
        },

        getSvcById(svcId) {
            return this.selectedServices.find(s => s.id === svcId) || null;
        },

        setServiceTier(svc, tier) {
            this.serviceTiers[svc.id] = tier;
            this.servicePrices[svc.id] = this.getTierPrice(svc, tier);
        },

        // Helper counts for service cards
        countUpperFields(svc) {
            return (svc.measurement_fields || []).filter(f => f.type === 'upper').length;
        },

        countLowerFields(svc) {
            return (svc.measurement_fields || []).filter(f => f.type === 'lower').length;
        },


        async openDropdown() {
            this.showDropdown = true;
            await this.runSearch();
        },

        async runSearch() {
            this.searchLoading = true;
            try {
                const d = await (await fetch('{{ route("api.orders.searchAll") }}?q=' + encodeURIComponent(this.searchQuery.trim()), { headers: { 'Accept': 'application/json' } })).json();
                this.searchResults = {
                    customers: Array.isArray(d.customers) ? d.customers : [],
                    members: Array.isArray(d.members) ? d.members : []
                };
            } catch (e) {
                this.searchResults = { customers: [], members: [] };
            }
            this.searchLoading = false;
        },

        async pickCustomer(c) {
            this.clearErrors();
            this.selectedCustomer = c;
            this.form.customer_id = c.id;
            this.soloMember = false;
            this.resetMemberAndBelow();
            this.searchQuery = '';
            this.showDropdown = false;
            await this.loadLedger(c);
            this.pickMember({ id: '__self__', name: c.name, relation: '', gender: c.gender, isSelf: true });
        },

        async pickMemberFromSearch(m) {
            if (!m.customer) return;
            this.clearErrors();
            this.selectedCustomer = m.customer;
            this.form.customer_id = m.customer.id;
            this.resetMemberAndBelow();
            this.searchQuery = '';
            this.showDropdown = false;
            await this.loadLedger(m.customer);
            this.pickMember({ id: m.id, name: m.name, relation: m.relation, gender: m.gender, isSelf: false });
            this.soloMember = true;
        },

        clearAll() {
            this.clearErrors();
            this.selectedCustomer = null;
            this.ledger = null;
            this.form.customer_id = '';
            this.searchQuery = '';
            this.resetMemberAndBelow();
        },

        resetMemberAndBelow() {
            this.selectedMemberId = null;
            this.soloMember = false;
            this.form.member_id = '';
            this.services = [];
            this.servicesLoading = false;
            this.resetServicesAndBelow();
        },

        resetServicesAndBelow() {
            this.selectedServices = [];
            this.serviceMeasurements = {};
            this.servicePrices = {};
            this.serviceQty = {};
            this.servicePrev = {};
            this.loadingServiceIds = [];
            this.form.paid_amount = '';
            this.form.account_id = '';
            this.form.due_date = this.calcDefaultDueDate(7);
            this.form.notes = '';
        },

        async loadLedger(c) {
            this.ledger = null;
            try {
                this.ledger = await (await fetch('{{ url("api/orders/customer-ledger") }}/' + c.id, { headers: { 'Accept': 'application/json' } })).json();
            } catch (e) {
                this.ledger = null;
            }
        },

        isMemberSelected(id) {
            if (id === undefined) return this.selectedMemberId !== null;
            return this.selectedMemberId === id;
        },

        pickMember(m) {
            this.clearErrors();
            this.selectedMemberId = m.id;
            this.form.member_id = m.id === '__self__' ? '' : m.id;
            this.services = [];
            this.resetServicesAndBelow();
            this.loadMemberServices();
        },

        selectedMemberName() {
            const m = this.memberOptions.find(x => x.id === this.selectedMemberId);
            return m ? (m.isSelf ? m.name + ' (Self)' : m.name) : '';
        },

        async loadMemberServices() {
            this.servicesLoading = true;
            try {
                const params = new URLSearchParams({ customer_id: this.form.customer_id });
                if (this.form.member_id) params.append('member_id', this.form.member_id);
                const rawServices = await (await fetch('{{ route("api.orders.memberServices") }}?' + params, { headers: { 'Accept': 'application/json' } })).json();
                // Deduplicate by id to prevent double entries
                const seenIds = new Set();
                this.services = rawServices.filter(s => {
                    if (seenIds.has(s.id)) return false;
                    seenIds.add(s.id);
                    return true;
                });
                if (!this.editMode && this.services.length > 0 && this.selectedServices.length === 0) {
                    this.addServiceRow();
                }
            } catch (e) {
                this.services = [];
            }
            this.servicesLoading = false;
        },

        async changeService(oldId, newId) {
            if (!newId || oldId == newId) return;
            const newSvc = this.services.find(s => s.id == newId);
            if (!newSvc) return;

            const oldIdx = this.selectedServices.findIndex(s => s.id === oldId);
            if (oldIdx > -1) {
                const prevQty = this.serviceQty[oldId] || 1;
                const prevNote = this.serviceNotes[oldId] || '';

                delete this.serviceMeasurements[oldId];
                delete this.servicePrices[oldId];
                delete this.serviceTiers[oldId];
                delete this.serviceQty[oldId];
                delete this.servicePrev[oldId];
                delete this.serviceNotes[oldId];

                this.selectedServices.splice(oldIdx, 1, newSvc);

                this.serviceTiers[newId] = 'standard';
                this.servicePrices[newId] = this.getTierPrice(newSvc, 'standard') || newSvc.price;
                this.serviceQty[newId] = prevQty;
                this.serviceNotes[newId] = prevNote;
                this.serviceMeasurements[newId] = {};
                this.servicePrev[newId] = null;
                await this.loadPreviousFor(newSvc);
            }
        },

        addServiceRow() {
            const uid = 'new_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
            const row = { id: uid, name: '', days: null, price: 0, pricing_tiers: null, measurement_fields: [] };
            this.selectedServices.push(row);
            this.serviceTiers[uid] = 'standard';
            this.servicePrices[uid] = 0;
            this.serviceQty[uid] = 1;
            this.serviceNotes[uid] = '';
            this.serviceMeasurements[uid] = {};
            this.servicePrev[uid] = null;
        },

        addFirstAvailableService() {
            this.addServiceRow();
        },

        isSelected(svc) {
            return this.selectedServices.some(x => x.id == svc.id);
        },

        async toggleService(svc) {
            this.clearErrors();
            const idx = this.selectedServices.findIndex(x => x.id === svc.id);
            if (idx > -1) {
                this.selectedServices.splice(idx, 1);
                delete this.serviceMeasurements[svc.id];
                delete this.servicePrices[svc.id];
                delete this.serviceTiers[svc.id];
                delete this.serviceQty[svc.id];
                delete this.servicePrev[svc.id];
                delete this.serviceNotes[svc.id];
                return;
            }
            this.selectedServices.push(svc);
            this.serviceTiers[svc.id] = 'standard';
            this.servicePrices[svc.id] = this.getTierPrice(svc, 'standard') || svc.price;
            this.serviceQty[svc.id] = 1;
            this.serviceNotes[svc.id] = '';
            this.serviceMeasurements[svc.id] = {};
            this.servicePrev[svc.id] = null;
            if (!this.form.due_date) {
                this.form.due_date = this.calcDefaultDueDate(svc.days || 7);
            }
            await this.loadPreviousFor(svc);
        },

        removeService(id) {
            this.clearErrors();
            this.selectedServices = this.selectedServices.filter(x => x.id !== id);
            delete this.serviceMeasurements[id];
            delete this.servicePrices[id];
            delete this.serviceTiers[id];
            delete this.serviceQty[id];
            delete this.servicePrev[id];
            delete this.serviceNotes[id];
        },

        // Smart measurement value matcher
        getSmartValue(fieldKey, rawData) {
            if (!rawData) return '';
            if (rawData[fieldKey] !== undefined && rawData[fieldKey] !== null && rawData[fieldKey] !== '') {
                return Array.isArray(rawData[fieldKey]) ? rawData[fieldKey].join(', ') : String(rawData[fieldKey]);
            }
            const aliasMap = {
                'kameez_length': ['length', 'shirt_length', 'upper_length'],
                'shirt_length': ['length', 'kameez_length', 'upper_length'],
                'coat_length': ['length', 'upper_length'],
                'kurta_length': ['length', 'kameez_length', 'upper_length'],
                'chest': ['chest', 'chaati'],
                'waist_upper': ['waist_upper', 'waist', 'kamar'],
                'waist': ['waist', 'waist_upper', 'kamar'],
                'shoulder': ['shoulder', 'shoulder_teera', 'teera'],
                'shoulder_teera': ['shoulder', 'teera'],
                'sleeves': ['sleeves', 'sleeve_length', 'sleeves_baazu', 'bazu'],
                'sleeve_length': ['sleeves', 'sleeves_baazu', 'bazu'],
                'sleeves_baazu': ['sleeves', 'sleeve_length', 'bazu'],
                'collar': ['collar', 'neck', 'neck_collar', 'gala'],
                'neck': ['collar', 'neck_collar', 'gala'],
                'neck_collar': ['collar', 'neck', 'gala'],
                'daman': ['daman', 'daman_ghera', 'ghera'],
                'daman_ghera': ['daman', 'ghera'],
                'cross_back': ['cross_back', 'peeth'],
                'bicep': ['bicep', 'muscle'],
                'wrist': ['wrist', 'cuff', 'mohri'],
                'shalwar_length': ['trouser_length', 'lower_length', 'pajama_length', 'pant_length', 'length'],
                'trouser_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pajama_length', 'pant_length', 'length'],
                'pant_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pajama_length', 'length'],
                'pajama_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pant_length', 'length'],
                'waist_lower': ['waist_lower', 'trouser_waist', 'lower_waist', 'pant_waist', 'waist'],
                'pant_waist': ['trouser_waist', 'lower_waist', 'waist_lower', 'waist'],
                'trouser_waist': ['trouser_waist', 'lower_waist', 'waist_lower', 'pant_waist', 'waist'],
                'hip': ['hip', 'seat'],
                'inseam': ['inseam'],
                'paincha': ['paincha', 'paincha_bottom', 'bottom_ankle', 'bottom'],
                'paincha_bottom': ['paincha', 'bottom_ankle', 'bottom'],
                'bottom_ankle': ['paincha', 'paincha_bottom', 'bottom'],
                'thigh': ['thigh', 'raan'],
                'asan': ['asan', 'shalwar_gher_asan', 'crotch'],
                'shalwar_gher_asan': ['asan', 'crotch']
            };
            const aliases = aliasMap[fieldKey.toLowerCase()] || [];
            for (const a of aliases) {
                if (rawData[a] !== undefined && rawData[a] !== null && rawData[a] !== '') {
                    return Array.isArray(rawData[a]) ? rawData[a].join(', ') : String(rawData[a]);
                }
            }
            return '';
        },

        getServiceMeasurementFields(svc) {
            if (svc && Array.isArray(svc.measurement_fields) && svc.measurement_fields.length > 0) {
                return svc.measurement_fields;
            }
            return [
                ...this.mUpperFields.map(f => ({ key: f.key, label: f.label, type: 'upper' })),
                ...this.mLowerFields.map(f => ({ key: f.key, label: f.label, type: 'lower' }))
            ];
        },

        async loadPreviousFor(svc) {
            this.loadingServiceIds.push(svc.id);
            let result = { measurements: {}, prev: null };
            try {
                const params = new URLSearchParams({
                    customer_id: this.form.customer_id,
                    service_id: svc.id,
                    member_id: this.form.member_id || ''
                });
                const r = await fetch('{{ route("api.orders.prevMeasurements") }}?' + params, { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                if (d.measurements && Object.keys(d.measurements).length > 0) {
                    const mapped = {};
                    const fields = this.getServiceMeasurementFields(svc);
                    fields.forEach(f => {
                        const sm = this.getSmartValue(f.key, d.measurements);
                        if (sm) mapped[f.key] = sm;
                    });
                    result = { measurements: mapped, prev: d };
                }
            } catch (e) {}
            this.servicePrev[svc.id] = result.prev;
            if (!this.serviceMeasurements[svc.id] || Object.keys(this.serviceMeasurements[svc.id]).length === 0) {
                this.serviceMeasurements[svc.id] = result.measurements;
            }
            this.loadingServiceIds = this.loadingServiceIds.filter(id => id !== svc.id);
        },

        getSmartHistory(fieldKey, historyObj, prevObj, existingObj) {
            const vals = [];
            const aliasMap = {
                'kameez_length': ['length', 'shirt_length', 'upper_length', 'kameez_length'],
                'shirt_length': ['length', 'kameez_length', 'upper_length', 'shirt_length'],
                'coat_length': ['length', 'upper_length', 'coat_length'],
                'kurta_length': ['length', 'kameez_length', 'upper_length', 'kurta_length'],
                'chest': ['chest', 'chaati'],
                'waist_upper': ['waist_upper', 'waist', 'kamar'],
                'waist': ['waist', 'waist_upper', 'kamar'],
                'shoulder': ['shoulder', 'shoulder_teera', 'teera'],
                'shoulder_teera': ['shoulder', 'teera'],
                'sleeves': ['sleeves', 'sleeve_length', 'sleeves_baazu', 'bazu'],
                'sleeve_length': ['sleeves', 'sleeves_baazu', 'bazu'],
                'sleeves_baazu': ['sleeves', 'sleeve_length', 'bazu'],
                'collar': ['collar', 'neck', 'neck_collar', 'gala'],
                'neck': ['collar', 'neck_collar', 'gala'],
                'neck_collar': ['collar', 'neck', 'gala'],
                'daman': ['daman', 'daman_ghera', 'ghera'],
                'daman_ghera': ['daman', 'ghera'],
                'cross_back': ['cross_back', 'peeth'],
                'bicep': ['bicep', 'muscle'],
                'wrist': ['wrist', 'cuff', 'mohri'],
                'shalwar_length': ['trouser_length', 'lower_length', 'pajama_length', 'pant_length', 'length', 'shalwar_length'],
                'trouser_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pajama_length', 'pant_length', 'length'],
                'pant_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pajama_length', 'length', 'pant_length'],
                'pajama_length': ['trouser_length', 'lower_length', 'shalwar_length', 'pant_length', 'length', 'pajama_length'],
                'waist_lower': ['waist_lower', 'trouser_waist', 'lower_waist', 'pant_waist', 'waist'],
                'pant_waist': ['trouser_waist', 'lower_waist', 'waist_lower', 'waist', 'pant_waist'],
                'trouser_waist': ['trouser_waist', 'lower_waist', 'waist_lower', 'pant_waist', 'waist'],
                'hip': ['hip', 'seat'],
                'inseam': ['inseam'],
                'paincha': ['paincha', 'paincha_bottom', 'bottom_ankle', 'bottom'],
                'paincha_bottom': ['paincha', 'bottom_ankle', 'bottom'],
                'bottom_ankle': ['paincha', 'paincha_bottom', 'bottom'],
                'thigh': ['thigh', 'raan'],
                'asan': ['asan', 'shalwar_gher_asan', 'crotch'],
                'shalwar_gher_asan': ['asan', 'crotch']
            };
            const keysToSearch = [fieldKey, ...(aliasMap[fieldKey.toLowerCase()] || [])];

            if (existingObj && existingObj[fieldKey]) {
                vals.push(...this.parseValues(existingObj[fieldKey]));
            }

            if (prevObj && prevObj.measurements) {
                for (const k of keysToSearch) {
                    const smartV = this.getSmartValue(k, prevObj.measurements);
                    if (smartV) {
                        vals.push(...this.parseValues(smartV));
                    }
                }
            }

            if (historyObj) {
                for (const hKey in historyObj) {
                    if (keysToSearch.some(k => k.toLowerCase() === hKey.toLowerCase())) {
                        const hVals = historyObj[hKey];
                        if (Array.isArray(hVals)) {
                            hVals.forEach(v => vals.push(...this.parseValues(v)));
                        } else if (hVals) {
                            vals.push(...this.parseValues(hVals));
                        }
                    }
                }
            }

            return [...new Set(vals.map(v => String(v).trim()).filter(v => v !== ''))];
        },

        async goMeasurements(svc) {
            this.mService = svc;
            this.mFieldErrors = {};
            this.mToast = '';
            this.loadMFieldsFromStorage();

            if (this.form.customer_id && (!this.servicePrev[svc.id] || !this.servicePrev[svc.id].all_history)) {
                await this.loadPreviousFor(svc);
            }

            const existing = this.serviceMeasurements[svc.id] || {};
            const prevObj = this.servicePrev[svc.id];
            this.mLoadedFromGeneral = !!prevObj;
            this.mSavedDate = prevObj?.saved_date || '';
            this.mCustomerStyles = prevObj?.measurements?.__style || null;

            // Merge any custom fields from existing order or prev measurements
            const customDefsToMerge = [];
            if (existing?.__custom_fields && Array.isArray(existing.__custom_fields)) {
                customDefsToMerge.push(...existing.__custom_fields);
            }
            if (prevObj?.measurements?.__custom_fields && Array.isArray(prevObj.measurements.__custom_fields)) {
                customDefsToMerge.push(...prevObj.measurements.__custom_fields);
            }
            customDefsToMerge.forEach(cf => {
                if (cf.section === 'lower') {
                    if (!this.mLowerFields.some(f => f.key === cf.key)) {
                        this.mLowerFields.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
                    }
                } else {
                    if (!this.mUpperFields.some(f => f.key === cf.key)) {
                        this.mUpperFields.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
                    }
                }
            });
            for (const k in existing) {
                if (k.startsWith('custom_u_') && !this.mUpperFields.some(f => f.key === k)) {
                    const cleanL = k.replace(/^custom_u_/, '').replace(/_\d+$/, '').replace(/_/g, ' ');
                    this.mUpperFields.push({ key: k, label: cleanL.charAt(0).toUpperCase() + cleanL.slice(1), isCustom: true });
                } else if (k.startsWith('custom_l_') && !this.mLowerFields.some(f => f.key === k)) {
                    const cleanL = k.replace(/^custom_l_/, '').replace(/_\d+$/, '').replace(/_/g, ' ');
                    this.mLowerFields.push({ key: k, label: cleanL.charAt(0).toUpperCase() + cleanL.slice(1), isCustom: true });
                }
            }
            this.saveMFieldsToStorage();

            // Build unified current measurements across ALL services as a fallback
            let unifiedCurrent = {};
            this.selectedServices.forEach(s => {
                if (s.id !== svc.id && this.serviceMeasurements[s.id]) {
                    Object.assign(unifiedCurrent, this.serviceMeasurements[s.id]);
                }
            });

            // Initialize values with smart mapping
            this.mValues = {};
            this.mInputShow = {};
            this.mOptionValues = {};
            this.mActiveUpperKeys = [];
            this.mActiveLowerKeys = [];

            const historyObj = prevObj?.all_history || {};

            // Populate upper fields
            this.mUpperFields.forEach(f => {
                let raw = existing[f.key];
                if (raw === undefined || raw === null || raw === '') {
                    raw = this.getSmartValue(f.key, existing);
                }
                
                if ((raw === undefined || raw === null || raw === '') && !this.editMode) {
                    if (unifiedCurrent[f.key]) {
                        raw = unifiedCurrent[f.key];
                    } else {
                        raw = this.getSmartValue(f.key, prevObj?.measurements);
                    }
                }
                
                const opts = this.getSmartHistory(f.key, historyObj, prevObj, existing);
                this.mOptionValues[f.key] = opts;

                const parsed = this.parseValues(raw);
                const valToUse = parsed.length > 0 ? parsed[0] : '';

                if (valToUse !== '') {
                    this.mValues[f.key] = valToUse;
                    this.mInputShow[f.key] = true;
                    this.mActiveUpperKeys.push(f.key);
                } else {
                    this.mValues[f.key] = '';
                }
            });

            // Populate lower fields
            this.mLowerFields.forEach(f => {
                let raw = existing[f.key];
                if (raw === undefined || raw === null || raw === '') {
                    raw = this.getSmartValue(f.key, existing);
                }
                
                if ((raw === undefined || raw === null || raw === '') && !this.editMode) {
                    if (unifiedCurrent[f.key]) {
                        raw = unifiedCurrent[f.key];
                    } else {
                        raw = this.getSmartValue(f.key, prevObj?.measurements);
                    }
                }

                const opts = this.getSmartHistory(f.key, historyObj, prevObj, existing);
                this.mOptionValues[f.key] = opts;

                const parsed = this.parseValues(raw);
                const valToUse = parsed.length > 0 ? parsed[0] : '';

                if (valToUse !== '') {
                    this.mValues[f.key] = valToUse;
                    this.mInputShow[f.key] = true;
                    this.mActiveLowerKeys.push(f.key);
                } else {
                    this.mValues[f.key] = '';
                }
            });

            this.upperLabelsOpen = false;
            this.lowerLabelsOpen = false;
            this.mModal = true;
        },

        mFields(svc) {
            if (!svc) return [];
            return this.getServiceMeasurementFields(svc).map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
        },

        mFieldErr(k) {
            return !!this.mFieldErrors[k];
        },

        saveMeasurements() {
            this.mFieldErrors = {};
            this.mToast = '';
            const svc = this.mService;
            if (!svc) return;

            const result = {};
            for (const key of Object.keys(this.mValues)) {
                const parsed = this.parseValues(this.mValues[key]);
                if (parsed.length > 0) {
                    result[key] = parsed[0];
                }
            }

            // Persist custom fields meta in service measurements if any
            const customUpper = this.mUpperFields.filter(f => f.isCustom).map(f => ({ section: 'upper', key: f.key, label: f.label, urdu: f.urdu }));
            const customLower = this.mLowerFields.filter(f => f.isCustom).map(f => ({ section: 'lower', key: f.key, label: f.label, urdu: f.urdu }));
            const allCustomDefs = [...customUpper, ...customLower];
            if (allCustomDefs.length > 0) {
                result.__custom_fields = allCustomDefs;
            }
            if (this.mCustomerStyles) {
                result.__style = this.mCustomerStyles;
            }

            this.serviceMeasurements[svc.id] = result;
            this.mModal = false;
        },

        mStatusLabel(id) {
            const data = this.serviceMeasurements[id] || {};
            const filled = Object.keys(data).filter(k => !k.startsWith('__') && this.parseValues(data[k]).length > 0).length;
            if (filled > 0) return '✓ Ready (' + filled + ' specs)';
            return '+ Add Specs';
        },

        mStatusClass(id) {
            const data = this.serviceMeasurements[id] || {};
            const filled = Object.keys(data).filter(k => !k.startsWith('__') && this.parseValues(data[k]).length > 0).length;
            if (filled > 0) return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            return 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100';
        },

        money(v) {
            return 'Rs. ' + Number(v || 0).toLocaleString('en-PK', { maximumFractionDigits: 2 });
        },

        clearErrors() {
            this.errors = {};
            this.fieldErrors = {};
        },

        validate() {
            this.clearErrors();
            let ok = true;
            if (!this.form.customer_id) { this.errors.customer = 'Please select a customer'; ok = false; }
            if (this.selectedMemberId === null) { this.errors.member = 'Please select a member'; ok = false; }
            const picked = this.selectedServices.filter(s => s.name);
            if (picked.length === 0) { this.errors.service = 'Please select at least one service'; ok = false; return ok; }

            let missing = [];
            this.selectedServices.forEach(svc => {
                if (!svc.name) { this.errors['service.' + svc.id] = 'Select a service for this row'; ok = false; return; }
                const qty = parseInt(this.serviceQty[svc.id]);
                if (!qty || qty < 1) {
                    this.errors['qty.' + svc.id] = 'Min 1';
                    ok = false;
                }
                if (!(parseFloat(this.servicePrices[svc.id]) >= 0)) {
                    this.errors['price.' + svc.id] = 'Enter a valid price';
                    ok = false;
                }
                this.mFields(svc).forEach(f => {
                    if (f.req) {
                        const v = (this.serviceMeasurements[svc.id]?.[f.k] ?? '').toString().trim();
                        if (!v) {
                            this.fieldErrors[svc.id + '.' + f.k] = true;
                            missing.push(svc.name + ': ' + f.l);
                        }
                    }
                });
            });

            if (missing.length > 0) {
                this.errors.measurements = 'Missing required measurements: ' + missing.join(', ');
                ok = false;
            }
            if ((parseFloat(this.form.paid_amount) || 0) < 0) { this.errors.paid_amount = 'Invalid amount'; ok = false; }
            if ((parseFloat(this.form.paid_amount) || 0) > 0 && !this.form.account_id && !this.editMode) {
                this.errors.account_id = 'Select an account for the advance payment';
                ok = false;
            }
            if (!this.form.due_date) { this.errors.due_date = 'Please select delivery due date'; ok = false; }
            return ok;
        },

        async saveOrder() {
            if (!this.validate()) {
                this.errors.general = 'Please review required fields before submitting.';

                // Smooth scroll within the real scrollable container (main)
                const scrollContainer = document.querySelector('main') || window;
                scrollContainer.scrollTo({ top: 0, behavior: 'smooth' });

                // If measurements are missing, prompt user with SweetAlert to open specs or continue!
                if (this.errors.measurements) {
                    if (typeof Swal !== 'undefined') {
                        const firstMissingSvc = this.selectedServices.find(s => {
                            return this.mFields(s).some(f => f.req && !(this.serviceMeasurements[s.id]?.[f.k] ?? '').toString().trim());
                        });

                        Swal.fire({
                            icon: 'warning',
                            title: 'Measurements Incomplete',
                            html: `<div class="text-sm text-left"><p class="mb-2 text-slate-600">The following required measurements are missing:</p><div class="p-2.5 bg-red-50 text-red-700 rounded-xl font-medium text-xs mb-3 border border-red-200">${this.errors.measurements}</div><p class="text-xs text-slate-500">Would you like to enter measurements now, or save order anyway?</p></div>`,
                            showCancelButton: true,
                            confirmButtonText: 'Add Measurements',
                            cancelButtonText: 'Save Order Anyway',
                            confirmButtonColor: '#4f46e5',
                            cancelButtonColor: '#64748b',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (firstMissingSvc) {
                                    this.goMeasurements(firstMissingSvc);
                                }
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                delete this.errors.measurements;
                                if (Object.keys(this.errors).filter(k => k !== 'general').length === 0) {
                                    this.errors = {};
                                    this.proceedSubmit();
                                }
                            }
                        });
                    }
                    return;
                }

                if (typeof Swal !== 'undefined') {
                    let errMsg = this.errors.customer || this.errors.service || this.errors.due_date || this.errors.account_id || this.errors.general;
                    Swal.fire({
                        icon: 'warning',
                        title: 'Incomplete Order Details',
                        text: errMsg,
                        confirmButtonColor: '#4f46e5'
                    });
                }
                return;
            }

            this.proceedSubmit();
        },

        async proceedSubmit() {
            this.submitting = true;
            await new Promise(res => setTimeout(res, 50));

            const form = document.getElementById('orderForm');
            const hCust = document.getElementById('hidden_customer_id');
            const hMemb = document.getElementById('hidden_member_id');
            const hDue = document.getElementById('hidden_due_date');
            const hNotes = document.getElementById('hidden_notes');
            const hPaid = document.getElementById('hidden_paid_amount');
            const hAcc = document.getElementById('hidden_account_id');

            if (hCust) hCust.value = this.form.customer_id || '';
            if (hMemb) hMemb.value = this.form.member_id || '';
            if (hDue) hDue.value = this.form.due_date || '';
            if (hNotes) hNotes.value = this.form.notes || '';
            if (hPaid) hPaid.value = this.form.paid_amount || 0;
            if (hAcc) hAcc.value = this.form.account_id || '';

            const container = document.getElementById('hidden_services_container');
            if (container) {
                container.innerHTML = '';
                this.selectedServices.filter(s => s.name).forEach(svc => {
                    const sId = document.createElement('input');
                    sId.type = 'hidden'; sId.name = 'service_ids[]'; sId.value = svc.id;
                    container.appendChild(sId);

                    const sTier = document.createElement('input');
                    sTier.type = 'hidden'; sTier.name = 'tiers[]'; sTier.value = this.getServiceTier(svc.id);
                    container.appendChild(sTier);

                    const sPrice = document.createElement('input');
                    sPrice.type = 'hidden'; sPrice.name = 'prices[]'; sPrice.value = this.servicePrices[svc.id] ?? svc.price;
                    container.appendChild(sPrice);

                    const sQty = document.createElement('input');
                    sQty.type = 'hidden'; sQty.name = 'quantities[]'; sQty.value = parseInt(this.serviceQty[svc.id]) || 1;
                    container.appendChild(sQty);

                    const sMeas = document.createElement('input');
                    sMeas.type = 'hidden'; sMeas.name = 'measurements_json[]';
                    sMeas.value = JSON.stringify(this.serviceMeasurements[svc.id] || {});
                    container.appendChild(sMeas);

                    const sNote = document.createElement('input');
                    sNote.type = 'hidden'; sNote.name = 'service_notes[]';
                    sNote.value = this.serviceNotes[svc.id] || '';
                    container.appendChild(sNote);
                });
            }

            form.submit();
        },

        // Quick Customer Action
        async saveQuickCustomer() {
            if (!this.qc.name.trim() || !this.qc.phone.trim()) {
                this.qcError = 'Name and phone number are required.';
                return;
            }
            this.qcSaving = true;
            this.qcError = '';
            try {
                const r = await fetch('{{ route("api.orders.quickCustomer") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(this.qc)
                });
                const d = await r.json();
                if (r.ok) {
                    this.showQuickCustomer = false;
                    this.qc = { name: '', phone: '', gender: 'male', address: '' };
                    await this.pickCustomer(d);
                } else {
                    this.qcError = d.message || 'Could not create customer.';
                }
            } catch (e) {
                this.qcError = 'Network error. Please try again.';
            }
            this.qcSaving = false;
        },

        // Quick Member Action
        async saveQuickMember() {
            if (!this.qm.name.trim() || !this.qm.relation) {
                this.qmError = 'Member name and relation are required.';
                return;
            }
            if (!this.selectedCustomer) {
                this.qmError = 'Please select customer first.';
                return;
            }
            this.qmSaving = true;
            this.qmError = '';
            try {
                const r = await fetch('{{ route("api.orders.quickMember") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ ...this.qm, customer_id: this.selectedCustomer.id })
                });
                const d = await r.json();
                if (r.ok) {
                    this.showQuickMember = false;
                    this.qm = { name: '', relation: '', gender: 'male' };
                    if (!this.selectedCustomer.members) this.selectedCustomer.members = [];
                    this.selectedCustomer.members.push(d);
                    this.pickMember(d);
                } else {
                    this.qmError = d.message || 'Could not create member.';
                }
            } catch (e) {
                this.qmError = 'Network error. Please try again.';
            }
            this.qmSaving = false;
        }
    };
}
</script>
@endpush
