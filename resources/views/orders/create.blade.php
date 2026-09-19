@extends('layouts.app')

@section('title', $editOrder ? 'Edit Order' : 'Create Order')

@section('content')
<div x-data="orderWizard()" x-init="init()" class="space-y-6">

    {{-- Top Navigation & Header (Matching services/create and measurements/create) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Orders
                </a>
                <span>/</span>
                <span class="text-slate-400">{{ $editOrder ? 'Edit Order #' . $editOrder->id : 'New Order' }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $editOrder ? 'Edit Order #' . $editOrder->id : 'Create Tailoring Order' }}</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Select customer, pick services with auto-filled measurements, and configure delivery schedule.</p>
                </div>
            </div>
        </div>

        {{-- Quick Header Actions --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-sm">
                Cancel
            </a>
            <button type="button" @click="saveOrder()" :disabled="submitting || !selectedCustomer || selectedServices.length === 0"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="submitting ? 'Saving...' : (editMode ? 'Update Order' : 'Save Order')"></span>
            </button>
        </div>
    </div>

    {{-- Server Error Banner --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-bold text-red-800">Order submission failed:</h4>
            <ul class="text-xs text-red-600 mt-1 list-disc list-inside space-y-0.5">
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
        <div class="lg:col-span-8 space-y-6">

            {{-- STEP 1: CUSTOMER + MEMBER + FINANCIAL LEDGER --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-base font-bold text-slate-800">Select Customer &amp; Family Member</h3>
                    </div>
                    <button type="button" @click="showQuickCustomer = true" x-show="!editMode"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
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

                {{-- Selected Customer Details & Member Selection (Matches measurements/create) --}}
                <div x-show="selectedCustomer" x-transition class="rounded-xl bg-slate-50 border border-slate-200/80 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-base flex items-center justify-center flex-shrink-0 shadow-sm"
                                x-text="selectedCustomer?.name?.charAt(0)?.toUpperCase()"></span>
                            <div>
                                <h4 class="font-bold text-slate-800 text-base leading-tight" x-text="selectedCustomer?.name"></h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span class="font-medium" x-text="selectedCustomer?.phone"></span>
                                    <span x-show="selectedCustomer?.gender" class="capitalize px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-600 text-[11px]" x-text="selectedCustomer?.gender"></span>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="clearAll()" title="Change Customer" x-show="!editMode"
                            class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-white rounded-lg border border-transparent hover:border-slate-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Member Selection Section --}}
                    <div class="mt-4 pt-3 border-t border-slate-200/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700">Order For:</span>
                            <button type="button" @click="showQuickMember = true" x-show="!editMode"
                                class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Add Family Member
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <template x-for="m in memberOptions" :key="'mo'+m.id">
                                <button type="button" @click="pickMember(m)"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
                                    :class="isMemberSelected(m.id)
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20'
                                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    <svg x-show="isMemberSelected(m.id)" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    <span x-text="m.isSelf ? 'Self (' + selectedCustomer?.name + ')' : m.name"></span>
                                    <span class="opacity-80 font-normal" x-show="!m.isSelf && m.relation" x-text="'(' + m.relation + ')'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Customer Financial Ledger Badges --}}
                    <div x-show="ledger" x-transition class="mt-3.5 pt-3 border-t border-slate-200/80 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-200/70 text-xs font-medium text-slate-700">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                            <span x-text="(ledger?.total_orders ?? 0) + ' past orders'"></span>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-200/70 text-xs font-medium text-slate-700">
                            Total: <strong class="ml-1 text-slate-800" x-text="money(ledger?.total_amount)"></strong>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-100/80 text-xs font-medium text-emerald-800">
                            Paid: <strong class="ml-1" x-text="money(ledger?.paid_amount)"></strong>
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold"
                            :class="parseFloat(ledger?.due_amount) > 0 ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-slate-100 text-slate-500'">
                            Due: <strong class="ml-1" x-text="money(ledger?.due_amount)"></strong>
                        </span>
                    </div>
                </div>
            </div>

            {{-- STEP 2: SERVICES (MULTI SELECT WITH UPPER/LOWER BREAKDOWN) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4" x-show="isMemberSelected()" x-transition>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                        <h3 class="text-base font-bold text-slate-800">Select Tailoring Services</h3>
                    </div>

                    <div class="flex items-center gap-3 ml-auto">
                        {{-- Search services --}}
                        <div class="relative w-48 sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                            </div>
                            <input type="text" x-model="serviceSearch" @input="servicePage = 0" placeholder="Search services..."
                                class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        </div>

                        {{-- Pagination --}}
                        <div class="flex items-center gap-1">
                            <button type="button" @click="servicePage = Math.max(0, servicePage - 1)" :disabled="servicePage === 0"
                                class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                            </button>
                            <span class="text-xs font-semibold text-slate-500 px-1" x-text="(visiblePages.length ? servicePage + 1 : 0) + ' / ' + visiblePages.length"></span>
                            <button type="button" @click="servicePage = Math.min(visiblePages.length - 1, servicePage + 1)" :disabled="servicePage >= visiblePages.length - 1"
                                class="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 transition disabled:opacity-30 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Loading indicator --}}
                <div x-show="servicesLoading" class="flex items-center justify-center gap-2 text-sm text-slate-400 py-6">
                    <svg class="animate-spin w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    <span>Loading available services...</span>
                </div>

                {{-- Service Cards Grid (Matching services/create style) --}}
                <div x-show="!servicesLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                    <template x-for="svc in currentPageServices" :key="svc.id">
                        <button type="button" @click="toggleService(svc)"
                            class="text-left p-4 rounded-xl border-2 transition-all duration-150 flex flex-col justify-between group relative overflow-hidden"
                            :class="isSelected(svc)
                                ? 'border-indigo-600 bg-indigo-50/70 shadow-sm ring-2 ring-indigo-200'
                                : 'border-slate-200 bg-white hover:border-slate-300 hover:shadow-xs'">
                            
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <span class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition" x-text="svc.name"></span>
                                <span class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0 text-white transition"
                                    :class="isSelected(svc) ? 'bg-indigo-600' : 'bg-slate-200 group-hover:bg-slate-300'">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs font-semibold pt-2 border-t border-slate-100">
                                <span class="text-indigo-600 font-bold" x-text="'From ' + money(getTierPrice(svc, 'basic'))"></span>
                                <span class="text-slate-400 font-normal" x-text="(svc.days || 5) + ' Days'"></span>
                            </div>

                            {{-- 3-Tier Quick Price Badges --}}
                            <div class="grid grid-cols-3 gap-1 mt-2 pt-1.5 border-t border-slate-100 text-[10px] text-center font-medium">
                                <span class="px-1 py-0.5 rounded bg-slate-100 text-slate-600 truncate" title="Basic stitching" x-text="'🥉 ' + money(getTierPrice(svc, 'basic'))"></span>
                                <span class="px-1 py-0.5 rounded bg-indigo-50 text-indigo-700 font-bold truncate" title="Standard stitching" x-text="'🥈 ' + money(getTierPrice(svc, 'standard'))"></span>
                                <span class="px-1 py-0.5 rounded bg-purple-50 text-purple-700 truncate" title="Premium stitching" x-text="'🥇 ' + money(getTierPrice(svc, 'premium'))"></span>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="!servicesLoading && services.length === 0" class="text-center py-6 text-sm text-slate-400">
                    No services found for this customer.
                </div>
            </div>

            {{-- STEP 3: SELECTED SERVICES & MEASUREMENTS TABLE --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4" x-show="selectedServices.length > 0" x-transition>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">3</span>
                        <h3 class="text-base font-bold text-slate-800">Order Items &amp; Measurements</h3>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-100"
                        x-text="selectedServices.length + ' Service' + (selectedServices.length > 1 ? 's' : '') + ' Selected'"></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50/80 border-y border-slate-200">
                            <tr class="text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="px-4 py-3 min-w-[340px]">Service &amp; Stitching Package</th>
                                <th class="px-3 py-3 text-center w-20">Qty</th>
                                <th class="px-3 py-3 text-right w-36">Unit Price</th>
                                <th class="px-3 py-3 text-right w-28">Total</th>
                                <th class="px-3 py-3 text-center w-40">Measurements</th>
                                <th class="px-3 py-3 text-center w-14">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <template x-for="svc in selectedServices" :key="'sel'+svc.id">
                                <tr class="hover:bg-slate-50/60 transition">
                                    {{-- Service Name & Stitching Package --}}
                                    <td class="px-4 py-3.5 align-middle">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800 text-sm" x-text="svc.name"></span>
                                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span x-text="(svc.days || 5) + ' Days'"></span>
                                            </span>
                                        </div>

                                        {{-- 3-Tier Stitching Package Selector (Inline Horizontal Segmented Pills) --}}
                                        <div class="inline-flex items-center p-1 bg-slate-100/90 rounded-xl border border-slate-200/80 gap-1 mt-2">
                                            <button type="button" @click="setServiceTier(svc, 'basic')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer"
                                                :class="getServiceTier(svc.id) === 'basic' ? 'bg-slate-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                                                <span>🥉 Basic</span>
                                                <span class="text-[11px] opacity-75 font-normal" x-text="'(' + money(getTierPrice(svc, 'basic')) + ')'"></span>
                                            </button>
                                            <button type="button" @click="setServiceTier(svc, 'standard')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer"
                                                :class="getServiceTier(svc.id) === 'standard' ? 'bg-indigo-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                                                <span>🥈 Standard</span>
                                                <span class="text-[11px] opacity-75 font-normal" x-text="'(' + money(getTierPrice(svc, 'standard')) + ')'"></span>
                                            </button>
                                            <button type="button" @click="setServiceTier(svc, 'premium')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold whitespace-nowrap transition cursor-pointer"
                                                :class="getServiceTier(svc.id) === 'premium' ? 'bg-purple-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60'">
                                                <span>🥇 Premium</span>
                                                <span class="text-[11px] opacity-75 font-normal" x-text="'(' + money(getTierPrice(svc, 'premium')) + ')'"></span>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Qty --}}
                                    <td class="px-3 py-3.5 text-center align-middle">
                                        <input type="number" min="1" step="1" x-model.number="serviceQty[svc.id]"
                                            class="w-14 py-1.5 px-2 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-bold text-center focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-2xs">
                                    </td>

                                    {{-- Unit Price --}}
                                    <td class="px-3 py-3.5 text-right align-middle">
                                        <div class="relative inline-block w-28">
                                            <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-[11px] font-semibold text-slate-400 pointer-events-none">Rs.</span>
                                            <input type="number" min="0" step="1" x-model.number="servicePrices[svc.id]"
                                                class="w-full pl-8 pr-2.5 py-1.5 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-bold text-right focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition shadow-2xs">
                                        </div>
                                    </td>

                                    {{-- Total --}}
                                    <td class="px-3 py-3.5 text-right font-extrabold text-slate-800 text-sm whitespace-nowrap align-middle"
                                        x-text="money((parseFloat(servicePrices[svc.id]) || 0) * (parseInt(serviceQty[svc.id]) || 1))"></td>

                                    {{-- Measurements Button --}}
                                    <td class="px-3 py-3.5 text-center align-middle whitespace-nowrap">
                                        <button type="button" @click="goMeasurements(svc)"
                                            class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold whitespace-nowrap transition shadow-xs cursor-pointer"
                                            :class="mStatusClass(svc.id)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                            <span x-text="mStatusLabel(svc.id)"></span>
                                        </button>
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-3 py-3.5 text-center align-middle">
                                        <button type="button" @click="removeService(svc.id)" title="Remove service"
                                            class="w-8 h-8 inline-flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer">
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

                {{-- Helper / Information Card (Matching services/create) --}}
                <div class="rounded-2xl bg-indigo-50/60 border border-indigo-100 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-indigo-900">Auto Measurement Sync</h5>
                            <p class="text-xs text-indigo-700/90 mt-0.5 leading-relaxed">
                                Measurements are automatically pulled from the customer's general body profile. You can tweak or override values for this specific order at any time.
                            </p>
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
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl z-10 max-h-[90vh] flex flex-col overflow-hidden">
            
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
            <div class="p-6 space-y-5 overflow-y-auto flex-1">
                
                {{-- Auto-fill from general measurements badge --}}
                <div x-show="mLoadedFromGeneral" class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs text-emerald-800">
                    <span class="flex items-center gap-2 font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Auto-filled from Customer's General Body Profile
                    </span>
                    <span class="text-[11px] text-emerald-600" x-text="'Saved ' + (mSavedDate || 'Profile')"></span>
                </div>

                {{-- Upper Body Fields Section --}}
                <div x-show="mUpperFields.length > 0" class="space-y-3">
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-xs font-bold text-indigo-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span>👕</span> Upper Body Specs
                        </span>
                        <span class="text-[11px] font-semibold text-slate-400" x-text="mUpperFields.length + ' fields'"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                        <template x-for="f in mUpperFields" :key="'mf_u_'+f.k">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    <span x-text="f.l"></span>
                                    <span x-show="f.req" class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.25" x-model="mValues[f.k]" placeholder="0.0"
                                        class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500"
                                        :class="mFieldErr(f.k) ? 'border-red-400 bg-red-50' : ''">
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs">in</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Lower Body Fields Section --}}
                <div x-show="mLowerFields.length > 0" class="space-y-3">
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-xs font-bold text-teal-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span>👖</span> Lower Body Specs
                        </span>
                        <span class="text-[11px] font-semibold text-slate-400" x-text="mLowerFields.length + ' fields'"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                        <template x-for="f in mLowerFields" :key="'mf_l_'+f.k">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    <span x-text="f.l"></span>
                                    <span x-show="f.req" class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.25" x-model="mValues[f.k]" placeholder="0.0"
                                        class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-teal-500"
                                        :class="mFieldErr(f.k) ? 'border-red-400 bg-red-50' : ''">
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs">in</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- General / Other Fields Section --}}
                <div x-show="mOtherFields.length > 0" class="space-y-3">
                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                            ⚙️ Other Measurements
                        </span>
                        <span class="text-[11px] font-semibold text-slate-400" x-text="mOtherFields.length + ' fields'"></span>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3.5">
                        <template x-for="f in mOtherFields" :key="'mf_o_'+f.k">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">
                                    <span x-text="f.l"></span>
                                    <span x-show="f.req" class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" x-model="mValues[f.k]" placeholder="Value"
                                        class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-indigo-500"
                                        :class="mFieldErr(f.k) ? 'border-red-400 bg-red-50' : ''">
                                    <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs">in</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Customer's Styling Preferences Display --}}
                <div x-show="mCustomerStyles && Object.keys(mCustomerStyles).length > 0" class="p-3.5 bg-amber-50/70 border border-amber-200/80 rounded-xl space-y-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1">
                        <span>✂️</span> Saved Style Preferences for this Customer:
                    </span>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <template x-for="(val, skey) in mCustomerStyles" :key="skey">
                            <span class="px-2.5 py-1 bg-white rounded-lg border border-amber-200 text-amber-900 font-semibold shadow-xs">
                                <span class="capitalize text-amber-600" x-text="skey.replace('_', ' ') + ': '"></span>
                                <span x-text="val"></span>
                            </span>
                        </template>
                    </div>
                </div>

                <p x-show="mToast" x-text="mToast" class="text-sm font-semibold text-red-500"></p>
            </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-200 bg-slate-50 flex-shrink-0">
                <button type="button" @click="mValues = {}" class="text-xs font-semibold text-slate-400 hover:text-red-500 transition">
                    Clear Inputs
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

        // Measurements modal state
        mModal: false,
        mSaving: false,
        mService: null,
        mValues: {},
        mCustomerStyles: null,
        mLoadedFromGeneral: false,
        mSavedDate: '',
        mFieldErrors: {},
        mToast: '',

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

        get visibleServices() {
            const q = (this.serviceSearch || '').trim().toLowerCase();
            if (!q) return this.services;
            return (this.services || []).filter(s => (s.name || '').toLowerCase().includes(q));
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
            if (svc.pricing_tiers && svc.pricing_tiers[tier] !== undefined) {
                return parseFloat(svc.pricing_tiers[tier]) || 0;
            }
            const base = parseFloat(svc.price) || 0;
            if (tier === 'basic') return Math.round(base * 0.75);
            if (tier === 'premium') return Math.round(base * 1.6);
            return base;
        },

        getServiceTier(serviceId) {
            return this.serviceTiers[serviceId] || 'standard';
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

        defaultUpperFields: [
            { k: 'kameez_length', l: 'Length (لمبائی)', req: 1 },
            { k: 'chest', l: 'Chest (چھاتی)', req: 1 },
            { k: 'waist_upper', l: 'Waist (کمر)', req: 0 },
            { k: 'shoulder', l: 'Shoulder (تیرا)', req: 1 },
            { k: 'sleeves', l: 'Sleeves (بازو)', req: 1 },
            { k: 'collar', l: 'Collar (کالر/گلا)', req: 1 },
            { k: 'daman', l: 'Daman (دامن)', req: 1 },
        ],

        defaultLowerFields: [
            { k: 'shalwar_length', l: 'Length (شلوار لمبائی)', req: 1 },
            { k: 'waist_lower', l: 'Waist (کمر)', req: 0 },
            { k: 'hip', l: 'Hip (ہپ/سیٹ)', req: 0 },
            { k: 'paincha', l: 'Paincha (پانچہ)', req: 1 },
            { k: 'thigh', l: 'Thigh (ران)', req: 0 },
            { k: 'asan', l: 'Asan (آسن)', req: 0 },
        ],

        // Categorized fields for the active modal service
        get mUpperFields() {
            if (this.mService && Array.isArray(this.mService.measurement_fields) && this.mService.measurement_fields.length > 0) {
                const list = this.mService.measurement_fields.filter(f => f.type === 'upper');
                if (list.length > 0) {
                    return list.map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
                }
            }
            return this.defaultUpperFields;
        },

        get mLowerFields() {
            if (this.mService && Array.isArray(this.mService.measurement_fields) && this.mService.measurement_fields.length > 0) {
                const list = this.mService.measurement_fields.filter(f => f.type === 'lower');
                if (list.length > 0) {
                    return list.map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
                }
            }
            return this.defaultLowerFields;
        },

        get mOtherFields() {
            if (!this.mService || !this.mService.measurement_fields) return [];
            return this.mService.measurement_fields
                .filter(f => f.type !== 'upper' && f.type !== 'lower')
                .map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
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
                delete this.serviceMeasurements[svc.id];
                delete this.servicePrices[svc.id];
                delete this.serviceTiers[svc.id];
                delete this.serviceQty[svc.id];
                delete this.servicePrev[svc.id];
                return;
            }
            this.selectedServices.push(svc);
            this.serviceTiers[svc.id] = 'standard';
            this.servicePrices[svc.id] = this.getTierPrice(svc, 'standard') || svc.price;
            this.serviceQty[svc.id] = 1;
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
        },

        // Smart measurement value matcher
        getSmartValue(fieldKey, rawData) {
            if (!rawData) return '';
            if (rawData[fieldKey] !== undefined && rawData[fieldKey] !== '') {
                return rawData[fieldKey];
            }
            const aliasMap = {
                'kameez_length': ['length', 'shirt_length', 'upper_length'],
                'shirt_length': ['length', 'kameez_length', 'upper_length'],
                'coat_length': ['length', 'upper_length'],
                'kurta_length': ['length', 'kameez_length', 'upper_length'],
                'chest': ['chest', 'chaati'],
                'waist': ['waist', 'kamar'],
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
                'pant_waist': ['trouser_waist', 'lower_waist', 'waist'],
                'trouser_waist': ['trouser_waist', 'lower_waist', 'pant_waist', 'waist'],
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
                    return Array.isArray(rawData[a]) ? rawData[a].join(', ') : rawData[a];
                }
            }
            return '';
        },

        getServiceMeasurementFields(svc) {
            if (svc && Array.isArray(svc.measurement_fields) && svc.measurement_fields.length > 0) {
                return svc.measurement_fields;
            }
            return [
                ...this.defaultUpperFields.map(f => ({ key: f.k, label: f.l, type: 'upper', required: f.req })),
                ...this.defaultLowerFields.map(f => ({ key: f.k, label: f.l, type: 'lower', required: f.req }))
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
                        mapped[f.key] = this.getSmartValue(f.key, d.measurements);
                    });
                    result = { measurements: mapped, prev: d };
                }
            } catch (e) {}
            this.serviceMeasurements[svc.id] = result.measurements;
            this.servicePrev[svc.id] = result.prev;
            this.loadingServiceIds = this.loadingServiceIds.filter(id => id !== svc.id);
        },

        goMeasurements(svc) {
            this.mService = svc;
            this.mFieldErrors = {};
            this.mToast = '';
            const existing = this.serviceMeasurements[svc.id] || {};
            const prevObj = this.servicePrev[svc.id];
            this.mLoadedFromGeneral = !!prevObj;
            this.mSavedDate = prevObj?.saved_date || '';
            this.mCustomerStyles = prevObj?.measurements?.__style || null;

            // Initialize values with smart mapping
            this.mValues = {};
            const fields = this.getServiceMeasurementFields(svc);
            fields.forEach(f => {
                this.mValues[f.key] = existing[f.key] ?? this.getSmartValue(f.key, prevObj?.measurements);
            });
            this.mModal = true;
        },

        mFields(svc) {
            if (!svc) return [];
            return this.getServiceMeasurementFields(svc).map(f => ({ k: f.key, l: f.label, req: f.required ? 1 : 0 }));
        },

        mFieldErr(k) {
            return !!this.mFieldErrors[k];
        },

        async saveMeasurements() {
            this.mFieldErrors = {};
            this.mToast = '';
            const svc = this.mService;
            if (!svc) return;

            let ok = true;
            this.mFields(svc).forEach(f => {
                if (f.req && !(this.mValues[f.k] || '').toString().trim()) {
                    this.mFieldErrors[f.k] = true;
                    ok = false;
                }
            });
            if (!ok) {
                this.mToast = 'Please fill all required (*) measurement fields';
                return;
            }

            this.serviceMeasurements[svc.id] = { ...this.mValues };
            this.mModal = false;
        },

        mStatusLabel(id) {
            const svc = this.selectedServices.find(x => x.id === id);
            const fields = svc ? this.mFields(svc) : [];
            if (!fields.length) return 'No Specs';
            const data = this.serviceMeasurements[id] || {};
            const filled = fields.filter(f => ((data[f.k] || '').toString().trim() !== '')).length;
            if (filled === fields.length && fields.length > 0) return '✓ Ready (' + filled + '/' + fields.length + ')';
            return (filled === 0 ? '+ Add Specs' : filled + '/' + fields.length + ' filled');
        },

        mStatusClass(id) {
            const svc = this.selectedServices.find(x => x.id === id);
            const fields = svc ? this.mFields(svc) : [];
            if (!fields.length) return 'bg-slate-100 text-slate-500';
            const data = this.serviceMeasurements[id] || {};
            const filled = fields.filter(f => ((data[f.k] || '').toString().trim() !== '')).length;
            if (filled === fields.length && fields.length > 0) return 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            return filled > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100';
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
            if (this.selectedServices.length === 0) { this.errors.service = 'Please select at least one service'; ok = false; return ok; }

            let missing = [];
            this.selectedServices.forEach(svc => {
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
                this.selectedServices.forEach(svc => {
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
