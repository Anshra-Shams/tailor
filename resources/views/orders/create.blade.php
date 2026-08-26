@extends('layouts.app')

@section('title', $editOrder ? 'Edit Order' : 'Create Order')

@section('content')
<div x-data="orderWizard()" class="max-w-6xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ================= LEFT COLUMN (WORK AREA) ================= --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- STEP 1: CUSTOMER + MEMBER + LEDGER --}}
            <section>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">1</span>
                    <h2 class="text-lg font-bold text-slate-800">Customer</h2>
                    @if($editOrder)
                        <span class="text-xs font-medium text-slate-400 bg-slate-100 rounded-full px-2.5 py-0.5">Edit mode</span>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">

                    {{-- Search --}}
                    <div class="relative" @click.outside="showDropdown = false" x-show="!editMode">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </div>
                        <input type="text" x-model="searchQuery"
                               @focus="openDropdown()"
                               @input.debounce.300ms="runSearch()"
                               placeholder="Search customer or member..."
                               class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="button" x-show="showDropdown" @click="showDropdown = false" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        {{-- Search results dropdown --}}
                        <div x-show="showDropdown" x-transition class="absolute left-0 right-0 z-30 mt-2 bg-white border border-slate-200 rounded-xl shadow-xl max-h-80 overflow-y-auto">
                            <div x-show="searchLoading" class="flex items-center justify-center gap-2 text-sm text-slate-400 py-5">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Searching...
                            </div>

                            <template x-if="customers.length > 0">
                                <div>
                                    <div class="px-4 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Customers</div>
                                    <template x-for="c in customers" :key="'c'+c.id">
                                        <button type="button" @click="pickCustomer(c)" class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition flex items-center gap-3">
                                            <span class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 font-semibold text-sm flex items-center justify-center flex-shrink-0" x-text="c.name?.charAt(0)?.toUpperCase()"></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm font-medium text-slate-700 truncate" x-text="c.name"></span>
                                                <span class="block text-xs text-slate-400 truncate" x-text="c.phone"></span>
                                            </span>
                                            <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <template x-if="members.length > 0">
                                <div>
                                    <div class="px-4 pt-3 pb-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400">Members</div>
                                    <template x-for="m in members" :key="'m'+m.id">
                                        <button type="button" @click="pickMemberFromSearch(m)" class="w-full text-left px-4 py-2.5 hover:bg-indigo-50 transition flex items-center gap-3">
                                            <span class="w-9 h-9 rounded-full bg-purple-100 text-purple-600 font-semibold text-sm flex items-center justify-center flex-shrink-0" x-text="m.name?.charAt(0)?.toUpperCase()"></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm font-medium text-slate-700 truncate">
                                                    <span x-text="m.name"></span>
                                                    <span x-show="m.relation" class="ml-1 text-xs text-slate-400 capitalize" x-text="'(' + m.relation + ')'"></span>
                                                </span>
                                                <span class="block text-xs text-slate-400 truncate">Member of <span class="font-medium text-slate-500" x-text="m.customer?.name"></span></span>
                                            </span>
                                            <svg class="w-4 h-4 text-slate-300 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                        </button>
                                    </template>
                                </div>
                            </template>

                            <p x-show="!searchLoading && customers.length === 0 && members.length === 0" class="px-4 py-8 text-center text-sm text-slate-400">No customers or members found</p>
                        </div>
                    </div>

                    {{-- Selected customer + members (inline) --}}
                    <div x-show="selectedCustomer" x-transition class="relative rounded-xl bg-slate-50 border border-slate-200 p-4">

                        {{-- Close / clear customer --}}
                        <button type="button" @click="clearAll()" title="Clear selected customer"
                                class="absolute top-3 right-3 w-7 h-7 flex items-center justify-center rounded-full text-slate-400 hover:text-red-500 hover:bg-white border border-transparent hover:border-red-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 pr-10">
                            {{-- Customer --}}
                            <span class="w-10 h-10 rounded-full bg-indigo-500 text-white font-semibold text-sm flex items-center justify-center flex-shrink-0" x-text="selectedCustomer?.name?.charAt(0)?.toUpperCase()"></span>
                            <div class="min-w-0 max-w-[180px] sm:max-w-[220px]">
                                <p class="font-bold text-slate-800 truncate leading-tight" x-text="selectedCustomer?.name"></p>
                                <p class="text-xs text-slate-500 truncate leading-tight mt-0.5" x-text="selectedCustomer?.phone"></p>
                            </div>

                            <span class="hidden md:block w-px self-stretch bg-slate-200 mx-1"></span>

                            {{-- Member pills (inline) --}}
                            <div class="flex flex-wrap items-center gap-2 min-w-0">
                                <template x-for="m in memberOptions" :key="'mo'+m.id">
                                    <button type="button" @click="pickMember(m)" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border-2 text-sm font-medium transition-all duration-150"
                                            :class="isMemberSelected(m.id) ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'border-slate-200 bg-white text-slate-600 hover:border-indigo-300 hover:bg-indigo-50'">
                                        <svg x-show="isMemberSelected(m.id)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        <span x-text="m.name"></span>
                                        <span class="text-xs opacity-75" x-text="memberSuffix(m)"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Ledger badges (compact) --}}
                        <div x-show="ledger" x-transition class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-xs font-medium text-slate-600">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                                <span x-text="(ledger?.total_orders ?? 0) + ' orders'"></span>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-slate-100 text-xs font-medium text-slate-600">
                                Total <span class="ml-1 font-bold text-slate-700" x-text="money(ledger?.total_amount)"></span>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-medium text-emerald-700">
                                Paid <span class="ml-1 font-bold" x-text="money(ledger?.paid_amount)"></span>
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-medium"
                                  :class="parseFloat(ledger?.due_amount) > 0 ? 'bg-red-50 border-red-100 text-red-600' : 'bg-slate-100 border-transparent text-slate-500'">
                                Due <span class="ml-1 font-bold" x-text="money(ledger?.due_amount)"></span>
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-red-500 pr-10" x-show="errors.member" x-text="errors.member"></p>
                    </div>

                    <p class="text-sm text-red-500 -mt-1" x-show="errors.customer" x-text="errors.customer"></p>
                </div>
            </section>

            {{-- STEP 2: SERVICES (MULTI SELECT) --}}
            <section x-show="isMemberSelected()" x-transition>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">2</span>
                    <h2 class="text-lg font-bold text-slate-800">Services</h2>
                    <span class="text-xs text-slate-400 hidden sm:inline">(select one or more)</span>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <p class="text-xs text-slate-400 mb-3">Only services with saved measurements for <span class="font-medium text-slate-600" x-text="selectedMemberName()"></span> are shown.</p>

                    <div x-show="servicesLoading" class="flex items-center justify-center gap-2 text-sm text-slate-400 py-4">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Loading...
                    </div>

                    <div x-show="!servicesLoading" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                        <template x-for="svc in services" :key="svc.id">
                            <button type="button" @click="toggleService(svc)" class="text-left p-4 rounded-xl border-2 transition-all duration-150" :class="isSelected(svc) ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-slate-200 bg-white hover:border-slate-300'">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-sm font-semibold text-slate-700" x-text="svc.name"></span>
                                    <span x-show="isSelected(svc)" class="text-indigo-500 flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 mt-1.5">
                                    <span class="text-indigo-500 font-medium" x-text="money(svc.price)"></span> &middot;
                                    <span x-text="svc.days + ' days'"></span>
                                </div>
                            </button>
                        </template>
                    </div>

                    <div x-show="!servicesLoading && services.length === 0" class="text-center py-8">
                        <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                        <p class="text-sm font-medium text-slate-600">No saved measurements for this member yet</p>
                        <a href="{{ route('measurements.create') }}" class="inline-flex items-center gap-1.5 mt-2 px-4 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">Add Measurement First</a>
                    </div>

                    <p class="mt-2 text-sm text-red-500" x-show="errors.service" x-text="errors.service"></p>
                </div>
            </section>

            {{-- STEP 3: MEASUREMENTS (ACCORDION PER SERVICE) --}}
            <section x-show="selectedServices.length > 0" x-transition>
                <div class="flex items-center gap-3 mb-3">
                    <span class="w-7 h-7 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">3</span>
                    <h2 class="text-lg font-bold text-slate-800">Measurements</h2>
                </div>

                <div class="space-y-3">
                    <template x-for="(svc, sIdx) in selectedServices" :key="'det'+svc.id">
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden" :class="hasSvcErrors(svc.id) ? 'border-red-300' : ''">

                            {{-- Accordion header --}}
                            <button type="button" @click="toggleAccordion(svc.id)" class="w-full flex items-center gap-3 px-4 py-3.5 text-left hover:bg-slate-50 transition">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-indigo-100 text-indigo-600 text-xs font-bold flex-shrink-0" x-text="sIdx + 1"></span>
                                <span class="text-sm font-bold text-slate-800 min-w-0 truncate flex-1" x-text="svc.name"></span>
                                <span class="hidden sm:inline text-xs text-slate-400 whitespace-nowrap">
                                    Qty <span class="font-semibold text-slate-600" x-text="(parseInt(serviceQty[svc.id]) || 1)"></span>
                                    &middot;
                                    <span class="font-semibold text-indigo-600" x-text="money((parseFloat(servicePrices[svc.id]) || 0) * (parseInt(serviceQty[svc.id]) || 1))"></span>
                                </span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 flex-shrink-0" :class="isOpen(svc.id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>

                            {{-- Accordion body --}}
                            <div x-show="isOpen(svc.id)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="px-4 pb-4 pt-1 border-t border-slate-100">

                                <div x-show="loadingServiceIds.includes(svc.id)" class="flex items-center gap-2 text-sm text-slate-400 py-3 justify-center">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Loading saved measurements...
                                </div>

                                <div x-show="servicePrev[svc.id] && !loadingServiceIds.includes(svc.id)" x-transition class="mb-3 bg-indigo-50 border border-indigo-200 rounded-xl px-3.5 py-2 text-xs text-indigo-700">
                                    <span class="font-semibold">Saved measurements loaded</span> (<span x-text="servicePrev[svc.id]?.saved_date"></span>) &mdash; edit if needed.
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-3">
                                    <template x-for="f in fieldsForSvc(svc)" :key="svc.id + '-' + f.k">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">
                                                <span x-text="f.l"></span>
                                                <span x-show="f.req" class="text-red-500">*</span>
                                            </label>
                                            <input type="text" inputmode="decimal" x-model="serviceMeasurements[svc.id][f.k]" placeholder="inches" class="w-full py-1.5 px-3 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" :class="fieldErr(svc.id, f.k) ? 'border-red-400 bg-red-50' : ''">
                                        </div>
                                    </template>
                                    <p x-show="fieldsForSvc(svc).length === 0" class="text-sm text-slate-400 col-span-full py-2">No measurement fields defined for this service.</p>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-3 max-w-sm">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Qty <span class="text-red-500">*</span></label>
                                        <input type="number" min="1" step="1" x-model.number="serviceQty[svc.id]" placeholder="1" class="w-full py-1.5 px-3 rounded-lg border text-sm focus:ring-indigo-500" :class="errors['qty.'+svc.id] ? 'border-red-400 bg-red-50' : 'border-slate-300 focus:border-indigo-500'">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-500 mb-1">Price / pc <span class="text-red-500">*</span></label>
                                        <input type="number" min="0" step="0.01" x-model.number="servicePrices[svc.id]" placeholder="0.00" class="w-full py-1.5 px-3 rounded-lg border text-sm focus:ring-indigo-500" :class="errors['price.'+svc.id] ? 'border-red-400 bg-red-50' : 'border-slate-300 focus:border-indigo-500'">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        {{-- ================= RIGHT COLUMN (CHECKOUT SIDEBAR) ================= --}}
        <div class="lg:col-span-4 lg:sticky lg:top-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                        Checkout
                    </h3>
                    <span class="text-xs font-medium text-slate-400" x-text="selectedServices.length + (selectedServices.length === 1 ? ' item' : ' items')"></span>
                </div>

                <div class="p-5 space-y-4">

                    {{-- Empty state --}}
                    <div x-show="selectedServices.length === 0" class="text-center py-6">
                        <svg class="w-8 h-8 mx-auto text-slate-200 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        <p class="text-sm text-slate-400">Select customer &amp; services to see summary</p>
                    </div>

                    <div x-show="selectedServices.length > 0" class="space-y-4">

                        {{-- Cost breakdown --}}
                        <div class="rounded-xl bg-slate-50 border border-slate-200 divide-y divide-slate-200 text-sm overflow-hidden">
                            <template x-for="svc in selectedServices" :key="'sum'+svc.id">
                                <div class="flex items-center justify-between px-3 py-2">
                                    <span class="text-slate-500 truncate pr-2 min-w-0">
                                        <span class="truncate" x-text="svc.name"></span>
                                        <span class="text-slate-300">&times;</span>
                                        <span class="font-medium text-slate-600" x-text="(parseInt(serviceQty[svc.id]) || 1)"></span>
                                    </span>
                                    <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money((parseFloat(servicePrices[svc.id]) || 0) * (parseInt(serviceQty[svc.id]) || 1))"></span>
                                </div>
                            </template>
                            <div class="flex items-center justify-between px-3 py-2.5 bg-white/70">
                                <span class="font-semibold text-slate-700">Total</span>
                                <span class="font-bold text-slate-900" x-text="money(totalPrice)"></span>
                            </div>
                        </div>

                        {{-- Payment inputs --}}
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Delivery Date <span class="text-red-500">*</span></label>
                                <input type="date" :min="today" x-model="form.due_date" class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500" :class="errors.due_date ? 'border-red-400 bg-red-50' : 'border-slate-300 focus:border-indigo-500'">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">Advance / Paid (Rs.)</label>
                                <input type="number" min="0" step="0.01" x-model="form.paid_amount" placeholder="0.00" class="w-full py-2 px-3 rounded-lg border text-sm focus:ring-indigo-500" :class="errors.paid_amount ? 'border-red-400 bg-red-50' : 'border-slate-300 focus:border-indigo-500'">
                            </div>
                        </div>

                        {{-- Remaining due highlight --}}
                        <div class="rounded-xl px-4 py-3 flex items-center justify-between" :class="remainingDue > 0 ? 'bg-red-50 border border-red-200' : 'bg-emerald-50 border border-emerald-200'">
                            <span class="text-sm font-medium" :class="remainingDue > 0 ? 'text-red-600' : 'text-emerald-700'" x-text="remainingDue > 0 ? 'Remaining Due' : 'Fully Paid'"></span>
                            <span class="text-lg font-bold" :class="remainingDue > 0 ? 'text-red-600' : 'text-emerald-700'" x-text="money(remainingDue)"></span>
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-xs font-medium text-slate-500 mb-1">Special Instructions</label>
                            <textarea x-model="form.notes" rows="2" placeholder="Any special instructions..." class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
                        </div>

                        {{-- Save button --}}
                        <button type="button" @click="saveOrder()" :disabled="submitting" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-md shadow-indigo-500/30 transition disabled:opacity-50">
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span x-text="submitting ? 'Saving...' : (editMode ? 'Update Order' : ('Save Order' + (selectedServices.length > 1 ? 's (' + selectedServices.length + ')' : '')))"></span>
                        </button>
                        <p class="text-sm text-red-500" x-show="errors.general" x-text="errors.general"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORM --}}
    <form id="orderForm" method="POST" action="{{ $editOrder ? route('orders.update', $editOrder) : route('orders.store') }}" style="display:none">
        @csrf
        @if($editOrder) @method('PUT') @endif
        <input type="hidden" name="customer_id" :value="form.customer_id">
        <input type="hidden" name="member_id"   :value="form.member_id">
        <input type="hidden" name="due_date"    :value="form.due_date">
        <input type="hidden" name="notes"       :value="form.notes">
        <input type="hidden" name="paid_amount" :value="form.paid_amount || 0">
        <template x-for="svc in selectedServices" :key="'hf'+svc.id">
            <div>
                <input type="hidden" name="service_ids[]"       :value="svc.id">
                <input type="hidden" name="prices[]"             :value="servicePrices[svc.id] ?? svc.price">
                <input type="hidden" name="quantities[]"         :value="parseInt(serviceQty[svc.id]) || 1">
                <input type="hidden" name="measurements_json[]"  :value="JSON.stringify(serviceMeasurements[svc.id] || {})">
            </div>
        </template>
    </form>
</div>
@endsection

@push('scripts')
<script>
window.__editOrder = @json($editOrder);
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
        selectedServices: [],
        serviceMeasurements: {},
        servicePrices: {},
        serviceQty: {},
        servicePrev: {},
        loadingServiceIds: [],
        openAccordions: [],
        submitting: false,
        today: new Date().toISOString().split('T')[0],
        form: { customer_id:'', member_id:'', paid_amount:'', due_date:'', notes:'' },
        errors: {},
        fieldErrors: {},

        async init() {
            if (this.editMode) {
                await this.initEditOrder();
            }
        },

        async initEditOrder() {
            const eo = window.__editOrder;
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
            const svc = this.services.find(s => s.id === eo.service_id);
            if (svc) {
                await this.toggleService(svc);
                this.servicePrices[svc.id] = eo.price;
                this.serviceQty[svc.id] = eo.quantity;
                const measurements = typeof eo.measurements === 'object' ? eo.measurements : JSON.parse(eo.measurements || '{}');
                this.serviceMeasurements[svc.id] = measurements;
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

        memberSuffix(m) {
            if (m.isSelf) return '(Self)';
            if (m.relation) return '(' + m.relation + ')';
            if (m.gender) return '(' + m.gender.charAt(0).toUpperCase() + m.gender.slice(1) + ')';
            return '';
        },

        isOpen(id) { return this.openAccordions.includes(id); },

        toggleAccordion(id) {
            if (this.isOpen(id)) {
                this.openAccordions = this.openAccordions.filter(x => x !== id);
            } else {
                this.openAccordions.push(id);
            }
        },

        hasSvcErrors(id) {
            return !!this.errors['qty.' + id] || !!this.errors['price.' + id] ||
                   Object.keys(this.fieldErrors).some(k => k.startsWith(id + '.'));
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
            this.pickMember({ id: m.id, name: m.name, relation: m.relation, gender: m.gender });
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
            this.openAccordions = [];
            this.form.paid_amount = '';
            this.form.due_date = '';
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
            if (this.soloMember && m.id === this.selectedMemberId) {
                this.soloMember = false;
                return;
            }
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
                this.services = await (await fetch('{{ route("api.orders.memberServices") }}?' + params, { headers: { 'Accept': 'application/json' } })).json();
            } catch (e) {
                this.services = [];
            }
            this.servicesLoading = false;
        },

        isSelected(svc) {
            return this.selectedServices.some(x => x.id === svc.id);
        },

        async toggleService(svc) {
            this.clearErrors();
            const idx = this.selectedServices.findIndex(x => x.id === svc.id);
            if (idx > -1) {
                this.selectedServices.splice(idx, 1);
                this.openAccordions = this.openAccordions.filter(x => x !== svc.id);
                delete this.serviceMeasurements[svc.id];
                delete this.servicePrices[svc.id];
                delete this.serviceQty[svc.id];
                delete this.servicePrev[svc.id];
                return;
            }
            this.selectedServices.push(svc);
            this.servicePrices[svc.id] = svc.price;
            this.serviceQty[svc.id] = 1;
            this.serviceMeasurements[svc.id] = {};
            this.servicePrev[svc.id] = null;
            this.openAccordions.push(svc.id);
            await this.loadPreviousFor(svc);
        },

        fieldsForSvc(svc) {
            if (!svc || !svc.measurement_fields) return [];
            return svc.measurement_fields.map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
        },

        async loadPreviousFor(svc) {
            this.loadingServiceIds.push(svc.id);
            let result = { measurements: {} , prev: null};
            try {
                const params = new URLSearchParams({
                    customer_id: this.form.customer_id,
                    service_id: svc.id,
                    member_id: this.form.member_id
                });
                const r = await fetch('{{ route("api.orders.prevMeasurements") }}?' + params, { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                if (d.measurements && Object.keys(d.measurements).length > 0) {
                    result = { measurements: { ...d.measurements }, prev: d };
                }
            } catch (e) {}
            this.serviceMeasurements[svc.id] = result.measurements;
            this.servicePrev[svc.id] = result.prev;
            this.loadingServiceIds = this.loadingServiceIds.filter(id => id !== svc.id);
        },

        fieldErr(serviceId, k) { return !!this.fieldErrors[serviceId + '.' + k]; },

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
            if (this.selectedServices.length === 0) { this.errors.service = 'Please select at least one service'; ok = false; return ok; }

            let missing = [];
            this.selectedServices.forEach(svc => {
                const qty = parseInt(this.serviceQty[svc.id]);
                if (!qty || qty < 1) {
                    this.errors['qty.' + svc.id] = 'Min 1';
                    ok = false;
                }
                if (!(parseFloat(this.servicePrices[svc.id]) > 0)) {
                    this.errors['price.' + svc.id] = 'Enter a valid price';
                    ok = false;
                }
                this.fieldsForSvc(svc).forEach(f => {
                    const v = (this.serviceMeasurements[svc.id]?.[f.k] ?? '').toString().trim();
                    if (!v) {
                        this.fieldErrors[svc.id + '.' + f.k] = true;
                        missing.push(svc.name + ': ' + f.l);
                    }
                });
                if (this.hasSvcErrors(svc.id) && !this.isOpen(svc.id)) {
                    this.openAccordions.push(svc.id);
                }
            });
            if (missing.length > 0) {
                this.errors.measurements = 'Fill required measurements — ' + missing.join(', ');
                ok = false;
            }
            if ((parseFloat(this.form.paid_amount) || 0) < 0) { this.errors.paid_amount = 'Invalid amount'; ok = false; }
            if (!this.form.due_date) { this.errors.due_date = 'Select delivery date'; ok = false; }
            return ok;
        },

        async saveOrder() {
            if (!this.validate()) {
                this.errors.general = 'Please fix the highlighted fields';
                return;
            }
            this.submitting = true;
            await new Promise(res => setTimeout(res, 0));
            document.getElementById('orderForm').submit();
        }
    };
}
</script>
@endpush
