@extends('layouts.app')

@section('title', 'Add Measurement')

@section('content')
<div x-data="wizard()" class="space-y-6">

    {{-- ================= STEP 1: CUSTOMER SELECTION ================= --}}
    <div x-show="step === 1" x-transition>
        <div class="max-w-lg mx-auto">
            <div class="mb-5">
                <h2 class="text-2xl font-bold text-slate-800">Measurements</h2>
                <p class="text-sm text-slate-500 mt-1">Select a customer to begin</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                {{-- Heading + Add Customer --}}
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">Customer</h3>
                    <button type="button" @click="showQuickCustomer=true" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add Customer
                    </button>
                </div>

                {{-- Search --}}
                <div class="relative mb-3" @click.outside="searchOpen = false">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg></div>
                    <input type="text" x-model="searchQuery" @focus="openSearch()" @input.debounce.300ms="searchCustomers()" placeholder="Search by name or phone..." class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="button" x-show="searchQuery" @click="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>

                {{-- Search results dropdown --}}
                <div x-show="searchOpen && searchResults.length > 0 && !selectedCustomer" x-transition class="mb-3 border border-slate-200 rounded-xl divide-y divide-slate-100 max-h-48 overflow-y-auto bg-white">
                    <template x-for="c in searchResults" :key="c.id">
                        <button type="button" @click="selectCustomer(c)" class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-slate-700" x-text="c.name"></div>
                                <div class="text-xs text-slate-400" x-text="c.phone"></div>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </template>
                </div>

                {{-- Loading spinner --}}
                <div x-show="searchLoading" class="flex items-center gap-2 text-sm text-slate-400 py-4 justify-center">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Searching...
                </div>

                {{-- Selected customer card --}}
                <div x-show="selectedCustomer" x-transition class="bg-indigo-50 border border-indigo-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0" x-text="selectedCustomer?.name?.charAt(0)?.toUpperCase()"></div>
                            <div>
                                <div class="text-base font-semibold text-indigo-800 leading-tight" x-text="selectedCustomer?.name"></div>
                                <div class="text-xs text-indigo-500 mt-0.5" x-text="selectedCustomer?.phone"></div>
                            </div>
                        </div>
                        <button type="button" @click="clearCustomer()" class="p-1.5 text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100 rounded-lg transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>

                    {{-- Select Member --}}
                    <div class="mt-4 pt-4 border-t border-indigo-100">
                        <h4 class="text-sm font-bold text-slate-800 mb-2">Select Member</h4>
                        <button type="button" @click="toggleMembers()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-indigo-700 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 transition">
                            Members
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showMembersList ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </button>

                        {{-- Members 2-col grid --}}
                        <div x-show="showMembersList" x-transition class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Self card --}}
                            <button type="button" @click="toggleMember({id:'__self__',name:selectedCustomer?.name,relation:'',gender:selectedCustomer?.gender})" class="text-left px-4 py-3 rounded-xl border-2 font-medium transition-all duration-150 text-sm flex items-center justify-between" :class="isMemberSelected('__self__') ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'">
                                <span><span x-text="selectedCustomer?.name"></span> <span class="text-xs opacity-60">(self)</span></span>
                                <span x-show="isMemberSelected('__self__')" class="text-indigo-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                            </button>
                            {{-- Member cards --}}
                            <template x-for="m in memberList" :key="m.id">
                                <button type="button" @click="toggleMember(m)" class="text-left px-4 py-3 rounded-xl border-2 font-medium transition-all duration-150 text-sm flex items-center justify-between" :class="isMemberSelected(m.id) ? 'border-indigo-500 bg-indigo-50 text-indigo-700 shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-slate-300'">
                                    <span>
                                        <span x-text="m.name"></span>
                                        <span x-show="m.relation" class="ml-1 text-xs opacity-60" x-text="'(' + m.relation + ')'"></span>
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <span class="text-xs opacity-50 capitalize" x-text="m.gender"></span>
                                        <span x-show="isMemberSelected(m.id)" class="text-indigo-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg></span>
                                    </span>
                                </button>
                            </template>
                            <p x-show="memberList.length === 0" class="text-xs text-slate-400 px-1 py-1 col-span-full">No members yet for this customer.</p>
                            {{-- Add New Member (full width) --}}
                            <button type="button" @click="showQuickMember=true" class="col-span-full py-3 border-2 border-dashed border-slate-300 rounded-xl text-sm font-semibold text-slate-500 hover:bg-white hover:border-indigo-400 hover:text-indigo-500 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Add New Member
                            </button>
                        </div>

                        <p x-show="!showMembersList && form.member_ids.length > 0" class="mt-3 text-xs text-slate-500">Adding measurement for: <span class="font-semibold text-indigo-700" x-text="selectedMembersName()"></span></p>
                    </div>
                </div>

                {{-- Error message --}}
                <p class="mt-3 text-sm text-red-500 min-h-[20px]" x-show="errors.step1" x-text="errors.step1" x-transition></p>
            </div>

            {{-- Next button (right-aligned below card) --}}
            <div class="flex justify-end mt-5">
                <button type="button" @click="next()" class="inline-flex items-center gap-1.5 px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm transition">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- ================= STEP 2: SERVICE + MEASUREMENTS + SAVE (FULL WIDTH) ================= --}}
    <div x-show="step === 2" x-transition class="space-y-6">

        {{-- Header --}}
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Measurements</h2>
            <p class="text-sm text-slate-500 mt-1">Customer: <span class="font-semibold text-slate-700" x-text="selectedMembersName()"></span></p>
        </div>

        {{-- Section: Select Service --}}
        <section>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2 mb-4">
                <h3 class="text-base font-bold text-slate-800 flex-shrink-0">Select Service</h3>
                <div class="flex-1"></div>
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="servicePrev()" :disabled="servicePage === 0" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    </button>
                    <button type="button" @click="serviceNext()" :disabled="servicePage >= serviceMaxPage" class="p-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </button>
                </div>
                <div class="relative w-full sm:w-56 sm:ml-auto">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    </div>
                    <input type="text" x-model="serviceSearchQuery" placeholder="Search services..." class="w-full pl-8 pr-3 py-1.5 rounded-lg border border-slate-200 text-xs bg-white focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-400">
                </div>
            </div>

            {{-- Service cards slider --}}
            <div class="overflow-hidden rounded-xl" style="height: 88px;">
                <div class="flex gap-4 transition-transform duration-300 ease-in-out h-full" :style="'transform: translateX(-' + (servicePage * 100) + '%)'">
                    <template x-for="(svc, idx) in filteredServices" :key="svc.id">
                        <button type="button" @click="selectService(svc)" class="flex-shrink-0 h-full min-w-0 text-left p-4 rounded-xl border transition-all duration-150" :style="'width: calc((100% / ' + servicePerPage + ') - 1rem)'" :class="form.service_id === svc.id ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'">
                            <div class="text-sm font-semibold text-slate-700 truncate" x-text="svc.name"></div>
                            <div class="text-xs text-slate-400 mt-1.5">
                                <span x-text="fieldsFor(svc.id).length + ' fields'"></span> &middot;
                                <span class="text-indigo-500 font-medium" x-text="fieldsFor(svc.id).filter(f=>f.req).length + ' required'"></span>
                            </div>
                        </button>
                    </template>
                </div>
                <p x-show="filteredServices.length === 0" class="text-sm text-slate-400 py-4 text-center">No services found.</p>
            </div>
            <p class="mt-2 text-sm text-red-500 min-h-[20px]" x-show="errors.step2" x-text="errors.step2" x-transition></p>
        </section>

        {{-- Shown after a service is selected --}}
        <div x-show="form.service_id" x-transition class="space-y-6">

            {{-- Section: Measurements --}}
            <section class="space-y-3">
                <div x-show="loading" class="flex items-center gap-2 text-sm text-slate-400 py-2 justify-center">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Loading previous measurements...
                </div>

                <div x-show="prevMeasure && !loading" x-transition class="bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-2.5 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="text-xs text-indigo-700"><span class="font-semibold">Previous measurements loaded</span> (saved <span x-text="prevMeasure?.saved_date"></span>) &mdash; edit before saving.</div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5">
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

            {{-- Section: Special Instructions --}}
            <section>
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <h3 class="text-base font-bold text-slate-800 mb-4">Special Instructions</h3>
                    <textarea x-model="form.notes" rows="2" placeholder="Any special instructions or notes..." class="w-full py-2 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
                </div>
            </section>

            {{-- Error + Action buttons --}}
            <p class="text-sm text-red-500 min-h-[20px]" x-show="errors.step3" x-text="errors.step3" x-transition></p>

            <div class="flex flex-wrap items-center gap-3">
                <button type="button" @click="prev()" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                    Back
                </button>
                <button type="button" @click="submitOrder()" :disabled="submitting" class="w-full sm:w-auto sm:ml-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition disabled:opacity-50">
                    <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    <svg x-show="submitting" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></circle></svg>
                    <span x-text="submitting ? 'Saving...' : 'Save Measurements'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- HIDDEN FORM (inside x-data scope) --}}
    <form id="orderForm" method="POST" action="{{ route('measurements.store') }}" style="display:none">
        @csrf
        <input type="hidden" name="customer_id"  :value="form.customer_id">
        <template x-for="mid in form.member_ids" :key="mid">
            <input type="hidden" name="member_ids[]" :value="mid === '__self__' ? '' : mid">
        </template>
        <input type="hidden" name="service_id"   :value="form.service_id">
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
                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label><input type="text" x-model="qc.name" class="w-full rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Full name"></div>
                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Phone <span class="text-red-500">*</span></label><input type="text" x-model="qc.phone" class="w-full rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="03XX-XXXXXXX"></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Gender</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="male" x-model="qc.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Male</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="female" x-model="qc.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Female</span></label>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Address</label><input type="text" x-model="qc.address" class="w-full rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Optional"></div>
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
                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Name <span class="text-red-500">*</span></label><input type="text" x-model="qm.name" class="w-full rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Member name"></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-2">Gender</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="male" x-model="qm.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Male</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="radio" value="female" x-model="qm.gender" class="text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-slate-600">Female</span></label>
                        </div>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-600 mb-1">Relation / Note</label><input type="text" x-model="qm.relation" class="w-full rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Son, Wife, Father"></div>
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
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]').content;

function wizard() {
    return {
        step: 1,
        loading: false,
        submitting: false,
        searchQuery: '',
        searchResults: [],
        searchLoading: false,
        searchOpen: false,
        selectedCustomer: null,
        selectedMember: null,
        services: [],
        serviceSearchQuery: '',
        servicePage: 0,
        servicePerPage: window.innerWidth < 640 ? 1 : (window.innerWidth < 1024 ? 2 : 4),
        memberList: [],
        showMembersList: false,
        showQuickCustomer: false,
        showQuickMember: false,
        prevMeasure: null,
        fieldErrors: {},
        qc: { name:'', phone:'', gender:'male', address:'' },
        qcSaving: false,
        qcError: '',
        qm: { name:'', gender:'male', relation:'' },
        qmSaving: false,
        qmError: '',
        form: { customer_id:'', member_ids:[], service_id:'', notes:'', measurements:{} },
        errors: { step1:'', step2:'', step3:'' },

        get filteredServices() {
            const q = this.serviceSearchQuery.trim().toLowerCase();
            if (!q) return this.services;
            return this.services.filter(s => s.name.toLowerCase().includes(q));
        },

        get serviceMaxPage() {
            return Math.max(0, Math.ceil(this.filteredServices.length / this.servicePerPage) - 1);
        },

        serviceNext() {
            if (this.servicePage < this.serviceMaxPage) this.servicePage++;
        },

        servicePrev() {
            if (this.servicePage > 0) this.servicePage--;
        },

        async init() {
            this.$watch('serviceSearchQuery', () => { this.servicePage = 0; });
            window.addEventListener('resize', () => {
                const w = window.innerWidth;
                const per = w < 640 ? 1 : (w < 1024 ? 2 : 4);
                if (per !== this.servicePerPage) {
                    this.servicePerPage = per;
                    if (this.servicePage > this.serviceMaxPage) this.servicePage = this.serviceMaxPage;
                }
            });
            try {
                this.services = await (await fetch('{{ route("api.orders.services") }}', { headers: { 'Accept': 'application/json' } })).json();
            } catch (e) {
                this.services = [];
            }
        },

        async searchCustomers() {
            this.searchLoading = true;
            try {
                this.searchResults = await (await fetch('{{ route("api.orders.searchCustomers") }}?q=' + encodeURIComponent(this.searchQuery.trim()), { headers: { 'Accept': 'application/json' } })).json();
            } catch (e) {
                this.searchResults = [];
            }
            this.searchLoading = false;
        },

        async openSearch() {
            if (this.selectedCustomer) return;
            this.searchOpen = true;
            if (!this.searchQuery.trim()) await this.searchCustomers();
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.form.customer_id = c.id;
            this.searchResults = [];
            this.searchQuery = '';
            this.searchOpen = false;
            this.errors.step1 = '';
            this.loadMembers(c);
        },

        async loadMembers(c) {
            this.memberList = Array.isArray(c.members) ? c.members : [];
            this.form.member_ids = ['__self__'];
            this.showMembersList = false;
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.searchLoading = false;
            this.searchOpen = false;
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.selectedMember = null;
            this.form.customer_id = '';
            this.form.member_ids = [];
            this.form.service_id = '';
            this.memberList = [];
            this.showMembersList = false;
            this.searchQuery = '';
            this.searchResults = [];
            this.prevMeasure = null;
            this.step = 1;
            this.errors.step1 = '';
        },

        toggleMembers() {
            this.showMembersList = !this.showMembersList;
        },

        selectMember(m) {
            this.form.member_ids = [m.id];
            this.showMembersList = false;
            this.errors.step1 = '';
        },

        toggleMember(m) {
            if (this.form.member_ids.length === 1 && this.form.member_ids[0] === m.id) {
                this.form.member_ids = [];
            } else {
                this.form.member_ids = [m.id];
            }
            this.errors.step1 = '';
            if (this.form.service_id) this.loadPrevious();
        },

        isMemberSelected(id) {
            return this.form.member_ids.includes(id);
        },

        selectedMembersName() {
            if (this.form.member_ids.length === 0) return '';
            const names = this.form.member_ids.map(id => {
                if (id === '__self__') return this.selectedCustomer?.name + ' (self)';
                const m = this.memberList.find(x => x.id === id);
                if (!m) return '';
                return m.relation ? m.name + ' (' + m.relation + ')' : m.name;
            }).filter(Boolean);
            return names.join(', ');
        },

        async saveQuickCustomer() {
            this.qcError = '';
            if (!this.qc.name.trim()) { this.qcError = 'Name is required'; return; }
            if (!this.qc.phone.trim()) { this.qcError = 'Phone is required'; return; }
            this.qcSaving = true;
            try {
                const r = await fetch('{{ route("api.orders.quickCustomer") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify(this.qc)
                });
                const d = await r.json();
                if (r.ok) {
                    this.selectCustomer(d);
                    this.showQuickCustomer = false;
                    this.qc = { name:'', phone:'', gender:'male', address:'' };
                } else {
                    this.qcError = d.message || 'Could not save customer';
                }
            } catch (e) {
                this.qcError = 'Network error';
            }
            this.qcSaving = false;
        },

        async saveQuickMember() {
            this.qmError = '';
            if (!this.qm.name.trim()) { this.qmError = 'Name is required'; return; }
            this.qmSaving = true;
            try {
                const r = await fetch('{{ route("api.orders.quickMember") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': TOKEN, 'Accept': 'application/json' },
                    body: JSON.stringify({ ...this.qm, customer_id: this.form.customer_id })
                });
                const d = await r.json();
                if (r.ok) {
                    this.memberList.push(d);
                    this.form.member_ids = [d.id];
                    this.showQuickMember = false;
                    this.qm = { name:'', gender:'male', relation:'' };
                } else {
                    this.qmError = d.message || 'Could not save member';
                }
            } catch (e) {
                this.qmError = 'Network error';
            }
            this.qmSaving = false;
        },

        async selectService(svc) {
            this.form.service_id = svc.id;
            this.errors.step2 = '';
            this.loading = true;
            this.prevMeasure = null;
            await this.loadPrevious();
            this.loading = false;
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
            return !!this.fieldErrors[k];
        },

        async loadPrevious() {
            this.prevMeasure = null;
            const mid = this.form.member_ids[0] === '__self__' ? '' : (this.form.member_ids[0] || '');
            try {
                const r = await fetch('{{ route("api.orders.prevMeasurements") }}?' + new URLSearchParams({
                    customer_id: this.form.customer_id,
                    service_id: this.form.service_id,
                    member_id: mid
                }), { headers: { 'Accept': 'application/json' } });
                const d = await r.json();
                if (d.measurements && Object.keys(d.measurements).length > 0) {
                    this.prevMeasure = d;
                    this.form.measurements = { ...d.measurements };
                } else {
                    this.form.measurements = {};
                }
            } catch (e) {
                this.form.measurements = {};
            }
        },

        async next() {
            if (this.step === 1) {
                if (!this.form.customer_id) { this.errors.step1 = 'Please select a customer'; return; }
                if (this.form.member_ids.length === 0) { this.errors.step1 = 'Please select a member'; return; }
                this.errors.step1 = '';
                this.step = 2;
            } else if (this.step === 2) {
                this.submitOrder();
            }
        },

        async submitOrder() {
            this.fieldErrors = {};
            this.errors.step3 = '';
            this.errors.step2 = '';
            if (!this.form.service_id) { this.errors.step2 = 'Pick a garment type'; return; }
            let ok = true;
            this.currentFields().forEach(f => {
                if (f.req && !(this.form.measurements[f.k] || '').trim()) {
                    this.fieldErrors[f.k] = true;
                    ok = false;
                }
            });
            if (!ok) {
                this.errors.step3 = 'Fill all required fields';
                return;
            }
            this.submitting = true;
            await new Promise(res => setTimeout(res, 0));
            document.getElementById('orderForm').submit();
        },

        prev() {
            if (this.step > 1) this.step--;
        }
    };
}
</script>
@endpush
