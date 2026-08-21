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
    <style>body{font-family:'Figtree',sans-serif}</style>
</head>
<body class="antialiased bg-slate-50 min-h-screen">
<header class="bg-white border-b border-slate-200 h-14 flex items-center px-4 sm:px-6 sticky top-0 z-30">
    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-indigo-600 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
        Orders
    </a>
    <div class="w-px h-5 bg-slate-200 mx-3"></div>
    <h1 class="text-sm font-semibold text-slate-800">New Order</h1>
</header>

<div class="max-w-xl mx-auto mt-6 sm:mt-10 mb-10 px-4" x-data="wizard()">
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="flex gap-1 px-6 pt-5 pb-1">
        <template x-for="i in 5"><div class="flex-1 h-1 rounded-full transition-colors duration-300" :class="step >= i ? 'bg-indigo-500' : 'bg-slate-200'"></div></template>
    </div>
    <div class="px-6 pb-6 pt-3">
        {{-- STEP 1: Main Customer --}}
        <div x-show="step===1" x-transition>
            <p class="text-xs text-slate-400 font-medium mb-1">Step 1 of 5</p>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-slate-800">Main Customer</h2>
                <button type="button" @click="showQuickCustomer=true" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add Customer
                </button>
            </div>
            <div class="relative mb-3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg></div>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchCustomers()" placeholder="Search by name or phone..." class="w-full pl-10 pr-10 py-2.5 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button x-show="searchQuery" @click="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div x-show="searchResults.length > 0 && !selectedCustomer" class="mb-3 border border-slate-200 rounded-lg divide-y divide-slate-100 max-h-48 overflow-y-auto">
                <template x-for="c in searchResults" :key="c.id">
                    <button type="button" @click="selectCustomer(c)" class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition flex items-center justify-between">
                        <div><div class="text-sm font-medium text-slate-700" x-text="c.name"></div><div class="text-xs text-slate-400" x-text="c.phone"></div></div>
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </template>
            </div>
            <div x-show="selectedCustomer" x-transition class="mb-3 bg-indigo-50 border border-indigo-200 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold text-sm" x-text="selectedCustomer?.name?.charAt(0)"></div>
                    <div><div class="text-sm font-semibold text-indigo-800" x-text="selectedCustomer?.name"></div><div class="text-xs text-indigo-500" x-text="selectedCustomer?.phone"></div></div>
                </div>
                <button @click="selectedCustomer=null; form.customer_id=''" class="p-1.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div x-show="searchLoading" class="flex items-center gap-2 text-sm text-slate-400 py-4 justify-center">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Searching...
            </div>
            <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step1" x-text="errors.step1" x-transition></p>
        </div>
        {{-- STEP 2: Member --}}
        <div x-show="step===2" x-transition>
            <p class="text-xs text-slate-400 font-medium mb-1">Step 2 of 5</p>
            <h2 class="text-base font-bold text-slate-800 mb-1">Who is this order for?</h2>
            <p class="text-xs text-slate-400 mb-4">For <span class="font-medium text-slate-500" x-text="selectedCustomer?.name"></span></p>
            <div class="flex flex-wrap gap-2 mb-3">
                <button type="button" @click="selectMember({id:'__self__',name:selectedCustomer?.name,relation:'',gender:selectedCustomer?.gender})" class="px-4 py-2.5 rounded-xl border-2 font-medium transition-all duration-150 text-sm" :class="form.member_id === '__self__' ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'">
                    <span x-text="selectedCustomer?.name"></span> <span class="text-xs opacity-50">(customer)</span>
                </button>
                <template x-for="m in memberList" :key="m.id">
                    <button type="button" @click="selectMember(m)" class="px-4 py-2.5 rounded-xl border-2 font-medium transition-all duration-150 text-sm" :class="form.member_id === m.id ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'">
                        <span x-text="m.name"></span>
                        <span x-show="m.relation" class="ml-1 text-xs opacity-60" x-text="'(' + m.relation + ')'"></span>
                        <span class="ml-1 text-xs opacity-50" x-text="m.gender"></span>
                    </button>
                </template>
            </div>
            <p x-show="memberList.length === 0" class="text-xs text-slate-400 mb-2">No members yet. Select customer above or add a new member.</p>
            <button @click="showQuickMember=true" type="button" class="w-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:border-slate-400 transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add New Member
            </button>
            <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step2" x-text="errors.step2" x-transition></p>
        </div>
        {{-- STEP 3: Service --}}
        <div x-show="step===3" x-transition>
            <p class="text-xs text-slate-400 font-medium mb-1">Step 3 of 5</p>
            <h2 class="text-base font-bold text-slate-800 mb-1">Select garment type</h2>
            <p class="text-xs text-slate-400 mb-4">For <span class="font-medium text-slate-500" x-text="selectedMemberName()"></span></p>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="svc in services" :key="svc.id">
                    <button type="button" @click="selectService(svc)" class="text-left p-3.5 rounded-xl border-2 transition-all duration-150" :class="form.service_id === svc.id ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                        <div class="text-sm font-semibold text-slate-700" x-text="svc.name"></div>
                        <div class="text-xs text-slate-400 mt-1">
                            <span x-text="fieldsFor(svc.id).length + ' fields'"></span> &middot;
                            <span class="text-indigo-500 font-medium" x-text="fieldsFor(svc.id).filter(f=>f.req).length + ' req'"></span>
                        </div>
                    </button>
                </template>
            </div>
            <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step3" x-text="errors.step3" x-transition></p>
        </div>
        {{-- STEP 4: Measurements --}}
        <div x-show="step===4" x-transition>
            <p class="text-xs text-slate-400 font-medium mb-1">Step 4 of 5</p>
            <h2 class="text-base font-bold text-slate-800 mb-1" x-text="serviceName() + ' measurements'"></h2>
            <p class="text-xs text-slate-400 mb-3">Fields change by garment type. <span class="text-red-500">*</span> required.</p>
            <div x-show="loading" class="flex items-center gap-2 text-sm text-slate-400 py-4 justify-center">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Loading previous measurements...
            </div>
            <div x-show="prevMeasure && !loading" x-transition class="mb-4 bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2.5 flex items-center gap-2.5">
                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="text-xs text-indigo-700"><span class="font-semibold">Previous measurements loaded</span> from Order #<span x-text="prevMeasure?.order_id"></span> (<span x-text="prevMeasure?.order_date"></span>) &mdash; edit before saving.</div>
            </div>
            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                <template x-for="f in currentFields()" :key="f.k">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 mb-1">
                            <span x-text="f.l"></span>
                            <span x-show="f.req" class="text-red-500">*</span>
                            <span x-show="!f.req" class="text-slate-400 font-normal">(optional)</span>
                        </label>
                        <input type="text" x-model="form.measurements[f.k]" placeholder="in inches" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" :class="fieldErr(f.k) ? 'border-red-400 bg-red-50' : ''">
                    </div>
                </template>
            </div>
            <div class="grid grid-cols-2 gap-x-4 mt-4 pt-4 border-t border-slate-100">
                <div><label class="block text-xs font-medium text-slate-500 mb-1">Price (Rs) <span class="text-red-500">*</span></label><input type="number" x-model="form.price" min="0" step="50" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                <div><label class="block text-xs font-medium text-slate-500 mb-1">Due date</label><input type="date" x-model="form.due_date" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
            </div>
            <div class="mt-3"><label class="block text-xs font-medium text-slate-500 mb-1">Notes</label><textarea x-model="form.notes" rows="2" placeholder="Special instructions..." class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea></div>
            <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step4" x-text="errors.step4" x-transition></p>
        </div>
        {{-- STEP 5: Summary --}}
        <div x-show="step===5" x-transition>
            <p class="text-xs text-slate-400 font-medium mb-1">Step 5 of 5</p>
            <h2 class="text-base font-bold text-slate-800 mb-4">Order Summary</h2>
            <div class="space-y-3">
                <div class="bg-slate-50 rounded-xl p-4"><div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium mb-1">Customer</div><div class="text-sm font-semibold text-slate-700" x-text="selectedCustomer?.name"></div><div class="text-xs text-slate-500" x-text="selectedCustomer?.phone"></div></div>
                <div class="bg-slate-50 rounded-xl p-4"><div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium mb-1">Member</div><div class="text-sm font-semibold text-slate-700" x-text="selectedMemberName()"></div></div>
                <div class="bg-slate-50 rounded-xl p-4"><div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium mb-1">Service</div><div class="text-sm font-semibold text-slate-700" x-text="serviceName()"></div></div>
                <div class="bg-slate-50 rounded-xl p-4">
                    <div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium mb-2">Measurements</div>
                    <div class="grid grid-cols-2 gap-2">
                        <template x-for="f in currentFields()" :key="f.k"><div class="flex justify-between text-xs"><span class="text-slate-500" x-text="f.l"></span><span class="font-medium text-slate-700" x-text="(form.measurements[f.k] || '\u2014') + (f.k !== 'description' ? ' in' : '')"></span></div></template>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-center"><div class="text-[11px] uppercase tracking-wide text-indigo-400 font-medium">Total Price</div><div class="text-xl font-bold text-indigo-700 mt-1" x-text="'Rs ' + Number(form.price).toLocaleString()"></div></div>
                    <div class="bg-slate-50 rounded-xl p-4 text-center"><div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium">Due Date</div><div class="text-sm font-semibold text-slate-700 mt-2" x-text="form.due_date || 'Not set'"></div></div>
                </div>
                <div x-show="form.notes" class="bg-slate-50 rounded-xl p-4"><div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium mb-1">Notes</div><div class="text-xs text-slate-600" x-text="form.notes"></div></div>
            </div>
        </div>
        {{-- Navigation --}}
        <div class="flex justify-between mt-6">
            <button type="button" @click="prev()" x-show="step > 1" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg> Back
            </button>
            <div x-show="step===1"></div>
            <button type="button" @click="next()" class="inline-flex items-center gap-1.5 px-5 py-2 text-sm font-semibold text-white rounded-lg transition shadow-sm" :class="step===5?'bg-emerald-600 hover:bg-emerald-700':'bg-indigo-600 hover:bg-indigo-700'">
                <span x-text="step===5?'Place Order':'Next'"></span>
                <svg x-show="step<5" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
            </button>
        </div>
    </div>
</div>
{{-- Hidden Form --}}
<form id="orderForm" method="POST" action="{{ route('orders.store') }}" style="display:none">
    @csrf
    <input type="hidden" name="customer_id"  :value="form.customer_id">
    <input type="hidden" name="member_id"    :value="form.member_id === '__self__' ? '' : form.member_id">
    <input type="hidden" name="service_id"   :value="form.service_id">
    <input type="hidden" name="price"        :value="form.price">
    <input type="hidden" name="due_date"     :value="form.due_date">
    <input type="hidden" name="notes"        :value="form.notes">
    <input type="hidden" name="measurements" :value="JSON.stringify(form.measurements)">
</form>
{{-- QUICK CUSTOMER MODAL --}}
<template x-if="showQuickCustomer">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showQuickCustomer=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">New Customer</h3>
                <button @click="showQuickCustomer=false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label><input type="text" x-model="qc.name" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Full name"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Phone <span class="text-red-500">*</span></label><input type="text" x-model="qc.phone" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="03XX-XXXXXXX"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-2">Gender</label><div class="flex gap-4"><label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="male" x-model="qc.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Male</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="female" x-model="qc.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Female</span></label></div></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Address</label><input type="text" x-model="qc.address" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional"></div>
            </div>
            <p x-show="qcError" x-text="qcError" class="mt-3 text-sm text-red-500" x-transition></p>
            <div class="flex justify-end gap-3 mt-6">
                <button @click="showQuickCustomer=false" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="saveQuickCustomer()" :disabled="qcSaving" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                    <span x-show="!qcSaving">Save &amp; Select</span><span x-show="qcSaving">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</template>
{{-- QUICK MEMBER MODAL --}}
<template x-if="showQuickMember">
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="showQuickMember=false"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-slate-800">New Member</h3>
                <button @click="showQuickMember=false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100"><svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label><input type="text" x-model="qm.name" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Member name"></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-2">Gender</label><div class="flex gap-4"><label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="male" x-model="qm.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Male</span></label><label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="female" x-model="qm.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Female</span></label></div></div>
                <div><label class="block text-sm font-medium text-slate-600 mb-1">Relation / Note</label><input type="text" x-model="qm.relation" class="w-full rounded-lg border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Son, Wife, Father"></div>
            </div>
            <p x-show="qmError" x-text="qmError" class="mt-3 text-sm text-red-500" x-transition></p>
            <div class="flex justify-end gap-3 mt-6">
                <button @click="showQuickMember=false" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">Cancel</button>
                <button @click="saveQuickMember()" :disabled="qmSaving" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                    <span x-show="!qmSaving">Save &amp; Select</span><span x-show="qmSaving">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</template>
</div>
<script>
const GARMENTS={
'Shalwar Kameez':{fields:[{k:'kurta_length',l:'Kurta Length',req:1},{k:'chest',l:'Chest',req:1},{k:'shoulder',l:'Shoulder',req:1},{k:'sleeve_len',l:'Sleeve Length',req:1},{k:'neck',l:'Neck',req:0},{k:'shalwar_len',l:'Shalwar Length',req:1},{k:'pancha',l:'Pancha',req:0}]},
'Suit Stitching':{fields:[{k:'chest',l:'Chest',req:1},{k:'waist',l:'Waist',req:1},{k:'shoulder',l:'Shoulder',req:1},{k:'sleeve_len',l:'Sleeve Length',req:1},{k:'collar',l:'Collar',req:0},{k:'jacket_len',l:'Jacket Length',req:1},{k:'trouser_len',l:'Trouser Length',req:1},{k:'trouser_waist',l:'Trouser Waist',req:1}]},
'Shirt Stitching':{fields:[{k:'chest',l:'Chest',req:1},{k:'shoulder',l:'Shoulder',req:1},{k:'sleeve_len',l:'Sleeve Length',req:1},{k:'neck',l:'Neck',req:1},{k:'shirt_len',l:'Shirt Length',req:1}]},
'Trouser':{fields:[{k:'waist',l:'Waist',req:1},{k:'length',l:'Length',req:1},{k:'thigh',l:'Thigh',req:0},{k:'knee',l:'Knee',req:0},{k:'pancha',l:'Pancha',req:1}]}
};
const DEF=[{k:'chest',l:'Chest',req:1},{k:'waist',l:'Waist',req:0},{k:'shoulder',l:'Shoulder',req:1},{k:'length',l:'Length',req:1},{k:'sleeve_len',l:'Sleeve Length',req:0}];
const TOKEN=document.querySelector('meta[name="csrf-token"]').content;
function wizard(){return{
step:1,loading:false,searchQuery:'',searchResults:[],searchLoading:false,
selectedCustomer:null,selectedMember:null,services:[],memberList:[],
showQuickCustomer:false,showQuickMember:false,prevMeasure:null,fieldErrors:{},
qc:{name:'',phone:'',gender:'male',address:''},qcSaving:false,qcError:'',
qm:{name:'',gender:'male',relation:''},qmSaving:false,qmError:'',
form:{customer_id:'',member_id:'',service_id:'',price:'',due_date:'',notes:'',measurements:{}},
errors:{step1:'',step2:'',step3:'',step4:''},
async init(){this.services=await(await fetch('{{route("api.orders.services")}}')).json()},
async searchCustomers(){if(this.searchQuery.length<2){this.searchResults=[];return}this.searchLoading=true;try{this.searchResults=await(await fetch('{{route("api.orders.searchCustomers")}}?q='+encodeURIComponent(this.searchQuery))).json()}catch(e){this.searchResults=[]}this.searchLoading=false},
selectCustomer(c){this.selectedCustomer=c;this.form.customer_id=c.id;this.searchResults=[];this.searchQuery=c.name;this.memberList=c.members||[]},
clearSearch(){this.searchQuery='';this.searchResults=[];this.selectedCustomer=null;this.form.customer_id='';this.memberList=[]},
async saveQuickCustomer(){this.qcError='';if(!this.qc.name.trim()){this.qcError='Name required';return}if(!this.qc.phone.trim()){this.qcError='Phone required';return}this.qcSaving=true;try{const r=await fetch('{{route("api.orders.quickCustomer")}}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':TOKEN,'Accept':'application/json'},body:JSON.stringify(this.qc)});const d=await r.json();if(r.ok){this.selectCustomer(d);this.showQuickCustomer=false;this.qc={name:'',phone:'',gender:'male',address:''}}else{this.qcError=d.message||'Error'}}catch(e){this.qcError='Network error'}this.qcSaving=false},
selectMember(m){this.selectedMember=m;this.form.member_id=m.id},
selectedMemberName(){if(!this.selectedMember)return'';if(this.selectedMember.id==='__self__')return this.selectedMember.name+' (customer)';let n=this.selectedMember.name;if(this.selectedMember.relation)n+=' ('+this.selectedMember.relation+')';return n},
async saveQuickMember(){this.qmError='';if(!this.qm.name.trim()){this.qmError='Name required';return}this.qmSaving=true;try{const r=await fetch('{{route("api.orders.quickMember")}}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':TOKEN,'Accept':'application/json'},body:JSON.stringify({...this.qm,customer_id:this.form.customer_id})});const d=await r.json();if(r.ok){this.memberList.push(d);this.selectMember(d);this.showQuickMember=false;this.qm={name:'',gender:'male',relation:''}}else{this.qmError=d.message||'Error'}}catch(e){this.qmError='Network error'}this.qmSaving=false},
selectService(svc){this.form.service_id=svc.id;this.form.price=''},
fieldsFor(svcId){const s=this.services.find(x=>x.id==svcId);return s?(GARMENTS[s.name]?.fields||DEF):[]},
currentFields(){return this.fieldsFor(this.form.service_id)},
serviceName(){const s=this.services.find(x=>x.id==this.form.service_id);return s?s.name:'Measurements'},
fieldErr(k){return this.fieldErrors[k]||false},
async loadPrevious(){this.prevMeasure=null;const mid=this.form.member_id==='__self__'?'':this.form.member_id;try{const r=await fetch('{{route("api.orders.prevMeasurements")}}?'+new URLSearchParams({customer_id:this.form.customer_id,service_id:this.form.service_id,member_id:mid}));const d=await r.json();if(d.measurements){this.prevMeasure=d;this.form.measurements={...d.measurements}}else{this.form.measurements={}}}catch(e){this.form.measurements={}}},
async next(){
if(this.step===1){if(!this.form.customer_id){this.errors.step1='Select a customer';return}this.errors.step1='';this.step=2}
else if(this.step===2){if(!this.form.member_id){this.errors.step2='Select who this order is for';return}this.errors.step2='';this.step=3}
else if(this.step===3){if(!this.form.service_id){this.errors.step3='Pick a garment type';return}this.errors.step3='';this.loading=true;this.step=4;await this.loadPrevious();this.loading=false}
else if(this.step===4){if(!this.form.price||this.form.price<=0){this.errors.step4='Enter a valid price';return}this.fieldErrors={};let ok=true;this.currentFields().forEach(f=>{if(f.req&&!this.form.measurements[f.k]?.trim()){this.fieldErrors[f.k]=true;ok=false}});if(!ok){this.errors.step4='Fill all required fields';return}this.errors.step4='';this.step=5}
else if(this.step===5){document.getElementById('orderForm').submit()}
},
prev(){if(this.step>1)this.step--}
}}
</script>
</body>
</html>
