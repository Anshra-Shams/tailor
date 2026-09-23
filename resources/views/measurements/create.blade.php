@extends('layouts.app')

@section('title', 'Add Customer Measurements')

@section('content')
<div x-data="measurementForm()" class="space-y-6">

    {{-- Top Navigation & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <a href="{{ route('measurements.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Measurements
                </a>
                <span>/</span>
                <span class="text-slate-400">New Customer Profile</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Customer General Measurements</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Record master body measurements. These general measurements will automatically apply across all tailoring orders.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ================= LEFT COLUMN: FORM (8 Cols) ================= --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Card 1: Customer & Member Selection --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-base font-bold text-slate-800">Select Customer &amp; Member</h3>
                    </div>
                    <button type="button" @click="showQuickCustomer = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-xl transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + New Customer
                    </button>
                </div>

                {{-- Search Bar (when customer is not yet selected) --}}
                <div x-show="!selectedCustomer" class="relative" @click.outside="searchOpen = false">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">
                        Search Customer <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </div>
                        <input type="text" x-model="searchQuery" @focus="openSearch()" @input.debounce.300ms="searchCustomers()"
                            placeholder="Search by customer name or phone number..."
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <button type="button" x-show="searchQuery" @click="clearSearch()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Search Results Dropdown --}}
                    <div x-show="searchOpen && searchResults.length > 0" x-transition
                        class="absolute left-0 right-0 top-full mt-1 z-30 bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100 max-h-56 overflow-y-auto">
                        <template x-for="c in searchResults" :key="c.id">
                            <button type="button" @click="selectCustomer(c)"
                                class="w-full text-left px-4 py-3 hover:bg-indigo-50 transition flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition"
                                        x-text="c.name.charAt(0).toUpperCase()"></span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800 group-hover:text-indigo-700" x-text="c.name"></p>
                                        <p class="text-xs text-slate-400" x-text="c.phone"></p>
                                    </div>
                                </div>
                                <span class="text-xs text-slate-400 group-hover:text-indigo-600 font-semibold flex items-center gap-1">
                                    Select
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                                </span>
                            </button>
                        </template>
                    </div>

                    <div x-show="searchLoading" class="mt-2 text-xs text-slate-400 flex items-center gap-2">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Searching customer directory...
                    </div>
                </div>

                {{-- Selected Customer Details & Member Pills --}}
                <div x-show="selectedCustomer" x-transition class="rounded-xl bg-slate-50 border border-slate-200/80 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-base flex items-center justify-center flex-shrink-0 shadow-sm"
                                x-text="selectedCustomer?.name?.charAt(0)?.toUpperCase()"></span>
                            <div>
                                <h4 class="font-bold text-slate-800 text-base leading-tight" x-text="selectedCustomer?.name"></h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span class="font-medium" x-text="selectedCustomer?.phone"></span>
                                    <span x-show="selectedCustomer?.gender" class="capitalize px-2 py-0.2 rounded-full bg-slate-200/80 text-slate-600 text-[11px]" x-text="selectedCustomer?.gender"></span>
                                </div>
                            </div>
                        </div>

                        <button type="button" @click="clearCustomer()" title="Change Customer"
                            class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-white rounded-lg border border-transparent hover:border-slate-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- Member Selection Pills --}}
                    <div class="mt-4 pt-3 border-t border-slate-200/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700">Record Measurements For:</span>
                            <button type="button" @click="showQuickMember = true"
                                class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                Add Family Member
                            </button>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            {{-- Self Pill --}}
                            <button type="button" @click="selectMember('__self__')"
                                class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
                                :class="selectedMemberId === '__self__'
                                    ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20'
                                    : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                <svg x-show="selectedMemberId === '__self__'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                <span>Self (<span x-text="selectedCustomer?.name"></span>)</span>
                            </button>

                            {{-- Other Member Pills --}}
                            <template x-for="m in memberList" :key="m.id">
                                <button type="button" @click="selectMember(m.id)"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1.5 shadow-xs"
                                    :class="selectedMemberId === m.id
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20'
                                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    <svg x-show="selectedMemberId === m.id" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    <span x-text="m.name"></span>
                                    <span x-show="m.relation" class="opacity-75 text-[11px]" x-text="'(' + m.relation + ')'"></span>
                                </button>
                            </template>
                        </div>

                        {{-- Previous Measurements Notice --}}
                        <div x-show="prevSavedDate" x-transition class="mt-3 flex items-center gap-2 p-2.5 rounded-lg bg-indigo-50/80 border border-indigo-100 text-xs text-indigo-700">
                            <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Existing profile found (saved <strong x-text="prevSavedDate"></strong>). You can update values below.</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Upper Body Measurements (Kameez, Shirt, Kurta, Coat) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                        <div class="flex items-center gap-2">
                            <span class="text-base">👕</span>
                            <h3 class="text-base font-bold text-slate-800">Upper Body Measurements</h3>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetUpperFields()"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 text-xs font-semibold transition cursor-pointer shadow-2xs"
                            title="Reset all Upper Body measurement buttons to default">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            <span>Reset</span>
                        </button>
                        <button type="button" @click="promptAddUpperField()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-dashed border-indigo-300 hover:border-indigo-500 bg-indigo-50/50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold transition-all cursor-pointer shadow-2xs"
                            title="Add a new custom measurement field">
                            <span class="text-sm font-extrabold leading-none">+</span>
                            <span>Add Field</span>
                        </button>
                        <button type="button"
                            @click="activeUpperKeys.length === upperFields.length ? hideAllUpper() : showAllUpper()"
                            :class="activeUpperKeys.length === upperFields.length ? 'bg-indigo-50 text-indigo-700 border-indigo-200 hover:bg-indigo-100' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border transition shadow-2xs">
                            <svg x-show="activeUpperKeys.length !== upperFields.length" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="activeUpperKeys.length === upperFields.length" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            <span x-text="activeUpperKeys.length === upperFields.length ? 'Hide All' : 'Show All'"></span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Label Buttons Bar --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-slate-500">
                            Click label buttons to open input fields <span class="text-slate-400 font-normal">(enter comma-separated values like 32, 34, 36)</span>:
                        </p>
                        <span class="text-[11px] text-slate-400" x-text="activeUpperKeys.length + ' selected'"></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <template x-for="f in upperFields" :key="f.key">
                            <button type="button"
                                @click="toggleUpperField(f.key)"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600 shadow-sm ring-2 ring-indigo-200': activeUpperKeys.includes(f.key),
                                    'bg-indigo-50/80 text-indigo-700 border-indigo-200 hover:bg-indigo-100': !activeUpperKeys.includes(f.key) && parseValues(measurements[f.key]).length > 0,
                                    'bg-white text-slate-700 border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40': !activeUpperKeys.includes(f.key) && parseValues(measurements[f.key]).length === 0
                                }"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold tracking-wide transition-all cursor-pointer select-none group shadow-xs hover:shadow">
                                <span class="text-[12px] font-black leading-none" x-text="activeUpperKeys.includes(f.key) ? '✓' : '+'"></span>
                                <span x-text="f.label"></span>
                                <span x-show="parseValues(measurements[f.key]).length > 0"
                                    :class="activeUpperKeys.includes(f.key) ? 'bg-white/25 text-white' : 'bg-indigo-200/90 text-indigo-900'"
                                    class="ml-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                                    x-text="parseValues(measurements[f.key]).length"></span>
                                <span @click.stop="confirmRemoveUpperField(f)"
                                    class="ml-1 opacity-50 hover:opacity-100 hover:text-red-500 font-bold text-xs transition"
                                    :title="'Remove ' + f.label + ' button'">&times;</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Dynamic Active Input Fields Grid --}}
                <div x-show="activeUpperKeys.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-2">
                    <template x-for="f in activeUpperFieldsList" :key="f.key">
                        <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200/90 rounded-xl p-3 transition focus-within:border-indigo-400 focus-within:ring-2 focus-within:ring-indigo-100">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-1.5 truncate">
                                    <label :for="'f_'+f.key" class="text-xs font-bold text-slate-800" x-text="f.label"></label>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="confirmRemoveUpperField(f)"
                                        class="text-slate-400 hover:text-red-500 p-0.5 rounded-md hover:bg-red-50 transition"
                                        :title="'Remove ' + f.label + ' button'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    <button type="button" @click="toggleUpperField(f.key)"
                                        class="text-slate-400 hover:text-slate-600 p-0.5 rounded-md hover:bg-slate-200 transition"
                                        title="Close this field">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text" :id="'f_'+f.key" x-model="measurements[f.key]"
                                    placeholder="e.g. 32, 34, 36"
                                    class="w-full pl-3 pr-8 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-800 placeholder-slate-300 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-xs text-slate-400 font-medium" x-text="unit"></span>
                            </div>
                            {{-- Live Parsed Values Preview --}}
                            <div x-show="parseValues(measurements[f.key]).length > 0" class="flex flex-wrap items-center gap-1 mt-2 pt-1.5 border-t border-slate-200/60">
                                <span class="text-[10px] text-slate-400 font-medium">Values:</span>
                                <template x-for="(val, idx) in parseValues(measurements[f.key])" :key="idx">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200"
                                        x-text="val + ' ' + unit"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty state when no upper fields active --}}
                <div x-show="activeUpperKeys.length === 0" class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    <span class="text-2xl block mb-1">👕</span>
                    <p class="text-xs font-medium text-slate-600">No upper body fields selected yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Click any button above to enter values, or click
                        <button type="button" @click="showAllUpper()" class="text-indigo-600 font-semibold hover:underline">Show All</button>.
                    </p>
                </div>
            </div>

            {{-- Card 3: Lower Body Measurements (Shalwar, Trouser, Pant, Pajama) --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">3</span>
                        <div class="flex items-center gap-2">
                            <span class="text-base">👖</span>
                            <h3 class="text-base font-bold text-slate-800">Lower Body Measurements</h3>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="resetLowerFields()"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-slate-500 hover:text-slate-700 text-xs font-semibold transition cursor-pointer shadow-2xs"
                            title="Reset all Lower Body measurement buttons to default">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            <span>Reset</span>
                        </button>
                        <button type="button" @click="promptAddLowerField()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-dashed border-emerald-300 hover:border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold transition-all cursor-pointer shadow-2xs"
                            title="Add a new custom measurement field">
                            <span class="text-sm font-extrabold leading-none">+</span>
                            <span>Add Field</span>
                        </button>
                        <button type="button"
                            @click="activeLowerKeys.length === lowerFields.length ? hideAllLower() : showAllLower()"
                            :class="activeLowerKeys.length === lowerFields.length ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-700 border-slate-200 hover:bg-slate-200'"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border transition shadow-2xs">
                            <svg x-show="activeLowerKeys.length !== lowerFields.length" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <svg x-show="activeLowerKeys.length === lowerFields.length" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/></svg>
                            <span x-text="activeLowerKeys.length === lowerFields.length ? 'Hide All' : 'Show All'"></span>
                        </button>
                    </div>
                </div>

                {{-- Interactive Label Buttons Bar --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-slate-500">
                            Click label buttons to open input fields <span class="text-slate-400 font-normal">(enter comma-separated values like 38, 40)</span>:
                        </p>
                        <span class="text-[11px] text-slate-400" x-text="activeLowerKeys.length + ' selected'"></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <template x-for="f in lowerFields" :key="f.key">
                            <button type="button"
                                @click="toggleLowerField(f.key)"
                                :class="{
                                    'bg-emerald-600 text-white border-emerald-600 shadow-sm ring-2 ring-emerald-200': activeLowerKeys.includes(f.key),
                                    'bg-emerald-50/80 text-emerald-700 border-emerald-200 hover:bg-emerald-100': !activeLowerKeys.includes(f.key) && parseValues(measurements[f.key]).length > 0,
                                    'bg-white text-slate-700 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40': !activeLowerKeys.includes(f.key) && parseValues(measurements[f.key]).length === 0
                                }"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold tracking-wide transition-all cursor-pointer select-none group shadow-xs hover:shadow">
                                <span class="text-[12px] font-black leading-none" x-text="activeLowerKeys.includes(f.key) ? '✓' : '+'"></span>
                                <span x-text="f.label"></span>
                                <span x-show="parseValues(measurements[f.key]).length > 0"
                                    :class="activeLowerKeys.includes(f.key) ? 'bg-white/25 text-white' : 'bg-emerald-200/90 text-emerald-900'"
                                    class="ml-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold"
                                    x-text="parseValues(measurements[f.key]).length"></span>
                                <span @click.stop="confirmRemoveLowerField(f)"
                                    class="ml-1 opacity-50 hover:opacity-100 hover:text-red-500 font-bold text-xs transition"
                                    :title="'Remove ' + f.label + ' button'">&times;</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Dynamic Active Input Fields Grid --}}
                <div x-show="activeLowerKeys.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-2">
                    <template x-for="f in activeLowerFieldsList" :key="f.key">
                        <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200/90 rounded-xl p-3 transition focus-within:border-emerald-400 focus-within:ring-2 focus-within:ring-emerald-100">
                            <div class="flex items-center justify-between mb-1.5">
                                <div class="flex items-center gap-1.5 truncate">
                                    <label :for="'f_'+f.key" class="text-xs font-bold text-slate-800" x-text="f.label"></label>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="confirmRemoveLowerField(f)"
                                        class="text-slate-400 hover:text-red-500 p-0.5 rounded-md hover:bg-red-50 transition"
                                        :title="'Remove ' + f.label + ' button'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    <button type="button" @click="toggleLowerField(f.key)"
                                        class="text-slate-400 hover:text-slate-600 p-0.5 rounded-md hover:bg-slate-200 transition"
                                        title="Close this field">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="text" :id="'f_'+f.key" x-model="measurements[f.key]"
                                    placeholder="e.g. 38, 40"
                                    class="w-full pl-3 pr-8 py-2 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-800 placeholder-slate-300 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-xs text-slate-400 font-medium" x-text="unit"></span>
                            </div>
                            {{-- Live Parsed Values Preview --}}
                            <div x-show="parseValues(measurements[f.key]).length > 0" class="flex flex-wrap items-center gap-1 mt-2 pt-1.5 border-t border-slate-200/60">
                                <span class="text-[10px] text-slate-400 font-medium">Values:</span>
                                <template x-for="(val, idx) in parseValues(measurements[f.key])" :key="idx">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200"
                                        x-text="val + ' ' + unit"></span>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty state when no lower fields active --}}
                <div x-show="activeLowerKeys.length === 0" class="text-center py-6 border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                    <span class="text-2xl block mb-1">👖</span>
                    <p class="text-xs font-medium text-slate-600">No lower body fields selected yet.</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Click any button above to enter values, or click
                        <button type="button" @click="showAllLower()" class="text-emerald-600 font-semibold hover:underline">Show All</button>.
                    </p>
                </div>
            </div>

            {{-- Card 4: Fitting, Style & Custom Measurement Parameters --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">4</span>
                        <h3 class="text-base font-bold text-slate-800">Cutting Styles &amp; Custom Fields</h3>
                    </div>
                    <button type="button" @click="addCustomField()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        + Add Custom Field
                    </button>
                </div>

                {{-- Style Options Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {{-- Fitting Type --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Fitting Preference</label>
                        <select x-model="stylePreferences.fitting" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs font-medium bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Regular Fit">Regular Fit (Standard)</option>
                            <option value="Slim Fit">Slim Fit (Fitting)</option>
                            <option value="Smart Fit">Smart Fit (Medium)</option>
                            <option value="Loose Fit">Loose Fit (Khula)</option>
                        </select>
                    </div>

                    {{-- Collar Style --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Collar / Neck Style</label>
                        <select x-model="stylePreferences.collar" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs font-medium bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Shirt Collar">Shirt Collar (Standard)</option>
                            <option value="Full Ban">Full Ban (Mandarin)</option>
                            <option value="Half Ban">Half Ban (Sherwani)</option>
                            <option value="Kurta Collar">Kurta Open Neck</option>
                        </select>
                    </div>

                    {{-- Daman Style --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Daman Style</label>
                        <select x-model="stylePreferences.daman" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs font-medium bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Choras">Choras (Square / Seedha)</option>
                            <option value="Gol">Gol (Round / Curve)</option>
                        </select>
                    </div>

                    {{-- Pocket Preference --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Pocket Style</label>
                        <select x-model="stylePreferences.pocket" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs font-medium bg-white focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Front Pocket + 2 Side">1 Front + 2 Side Pockets</option>
                            <option value="Front Pocket Only">1 Front Pocket Only</option>
                            <option value="Double Front Pocket">Double Front Pocket</option>
                            <option value="No Front Pocket">Side Pockets Only</option>
                        </select>
                    </div>
                </div>

                {{-- Custom Dynamic Fields List --}}
                <div x-show="customFields.length > 0" class="space-y-3 pt-3 border-t border-slate-100">
                    <span class="text-xs font-bold text-slate-700">Custom Measurement Fields:</span>
                    <template x-for="(cf, idx) in customFields" :key="idx">
                        <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                            <input type="text" x-model="cf.label" placeholder="Measurement Name (e.g. Belt, Pocket Width...)"
                                class="flex-1 px-3 py-1.5 bg-white rounded-lg border border-slate-300 text-xs font-medium">
                            <div class="relative w-32">
                                <input type="text" x-model="cf.value" placeholder="0.0"
                                    class="w-full pl-3 pr-7 py-1.5 bg-white rounded-lg border border-slate-300 text-xs font-bold">
                                <span class="absolute inset-y-0 right-0 pr-2 flex items-center text-[11px] text-slate-400" x-text="unit"></span>
                            </div>
                            <button type="button" @click="removeCustomField(idx)" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>


            </div>

            {{-- Bottom Save Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('measurements.index') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-sm">
                    Cancel
                </a>
                <button type="button" @click="saveMeasurements()" :disabled="saving || !selectedCustomer"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-500/25 hover:shadow-indigo-500/35 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="saving ? 'Saving...' : 'Save General Measurements'"></span>
                </button>
            </div>
        </div>

        {{-- ================= RIGHT COLUMN: STICKY LIVE SUMMARY (4 Cols) ================= --}}
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-6 space-y-5">

                {{-- Summary Card --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Measurement Profile</span>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400">Master Record</span>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Customer info --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer Profile</span>
                            <h4 class="text-base font-bold text-slate-800 mt-0.5" x-text="selectedCustomer ? selectedCustomer.name : 'No Customer Selected'"></h4>
                            <p class="text-xs text-slate-500" x-text="selectedCustomer ? selectedCustomer.phone : 'Search and select a customer on the left'"></p>
                            <p x-show="selectedCustomer && selectedMemberId !== '__self__'" class="text-xs text-indigo-600 font-semibold mt-1">
                                👤 Member: <span x-text="selectedMemberName"></span>
                            </p>
                        </div>

                        {{-- Progress Counts --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-slate-500 font-medium">Total Measurements Filled</span>
                                <span class="font-bold text-slate-800" x-text="(upperFilledCount + lowerFilledCount) + ' values' "></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2 rounded-full transition-all duration-300"
                                    :style="'width: ' + (((upperFilledCount + lowerFilledCount) / (upperFields.length + lowerFields.length)) * 100) + '%'"></div>
                            </div>
                        </div>

                        {{-- Upper & Lower Badges --}}
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="p-3 bg-indigo-50/70 border border-indigo-100 rounded-xl">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-indigo-600">👕 Upper Body</span>
                                <span class="text-base font-extrabold text-indigo-900" x-text="upperFilledCount + ' / ' + upperFields.length"></span>
                            </div>
                            <div class="p-3 bg-emerald-50/70 border border-emerald-100 rounded-xl">
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-emerald-700">👖 Lower Body</span>
                                <span class="text-base font-extrabold text-emerald-900" x-text="lowerFilledCount + ' / ' + lowerFields.length"></span>
                            </div>
                        </div>

                        {{-- Style Selections --}}
                        <div class="pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Fitting:</span>
                                <span class="font-bold text-slate-700" x-text="stylePreferences.fitting"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Collar:</span>
                                <span class="font-bold text-slate-700" x-text="stylePreferences.collar"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Daman:</span>
                                <span class="font-bold text-slate-700" x-text="stylePreferences.daman"></span>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-3 border-t border-slate-100">
                            <button type="button" @click="saveMeasurements()" :disabled="saving || !selectedCustomer"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-md shadow-indigo-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-sm">
                                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span x-text="saving ? 'Saving...' : 'Save Measurements'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Helper Card --}}
                <div class="rounded-2xl bg-indigo-50/60 border border-indigo-100 p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="text-xs font-bold text-indigo-900">Universal Availability</h5>
                            <p class="text-xs text-indigo-700/90 mt-0.5 leading-relaxed">
                                Once saved, these general measurements are stored directly under the customer's profile. When taking an order for any service, these values will automatically load!
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- QUICK CUSTOMER MODAL --}}
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
                        <input type="text" x-model="qc.address" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Optional">
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

    {{-- QUICK MEMBER MODAL --}}
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
                        <input type="text" x-model="qm.name" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Member name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Relation</label>
                        <input type="text" x-model="qm.relation" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Son, Brother, Father, Wife">
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
                        <span x-text="qmSaving ? 'Saving...' : 'Save & Select'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
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

function measurementForm() {
    return {
        unit: 'in', // inches

        // Customer & Member
        selectedCustomer: null,
        selectedMemberId: '__self__',
        memberList: [],
        searchQuery: '',
        searchResults: [],
        searchOpen: false,
        searchLoading: false,

        prevSavedDate: null,
        saving: false,
        notes: '',

        // Quick Customer modal
        showQuickCustomer: false,
        qc: { name: '', phone: '', gender: 'male', address: '' },
        qcSaving: false,
        qcError: '',

        // Quick Member modal
        showQuickMember: false,
        qm: { name: '', relation: '', gender: 'male' },
        qmSaving: false,
        qmError: '',

        // Default field definitions for reset
        defaultUpperFields: DEFAULT_UPPER_FIELDS,
        defaultLowerFields: DEFAULT_LOWER_FIELDS,

        // Upper Body Fields (persistently stored in browser localStorage)
        upperFields: getStoredMeasurementFields('tailor_upper_fields', DEFAULT_UPPER_FIELDS),

        // Lower Body Fields (persistently stored in browser localStorage)
        lowerFields: getStoredMeasurementFields('tailor_lower_fields', DEFAULT_LOWER_FIELDS),

        saveFieldsToStorage() {
            try {
                localStorage.setItem('tailor_upper_fields', JSON.stringify(this.upperFields));
                localStorage.setItem('tailor_lower_fields', JSON.stringify(this.lowerFields));
            } catch (e) {
                console.error('Failed to save measurement fields to localStorage', e);
            }
        },

        loadFieldsFromStorage() {
            this.upperFields = getStoredMeasurementFields('tailor_upper_fields', DEFAULT_UPPER_FIELDS);
            this.lowerFields = getStoredMeasurementFields('tailor_lower_fields', DEFAULT_LOWER_FIELDS);
        },

        // Active / open measurement field keys
        activeUpperKeys: [],
        activeLowerKeys: [],

        // Measurement Values container
        measurements: {},

        // Style preferences
        stylePreferences: {
            fitting: 'Regular Fit',
            collar: 'Shirt Collar',
            daman: 'Choras',
            pocket: 'Front Pocket + 2 Side'
        },

        // Custom Fields
        customFields: [],

        get selectedMemberName() {
            if (this.selectedMemberId === '__self__') {
                return this.selectedCustomer?.name || 'Self';
            }
            const m = this.memberList.find(i => String(i.id) === String(this.selectedMemberId));
            return m ? (m.name + (m.relation ? ' (' + m.relation + ')' : '')) : 'Self';
        },

        parseValues(val) {
            if (val === undefined || val === null) return [];
            if (Array.isArray(val)) {
                return val.map(v => String(v).trim()).filter(v => v !== '');
            }
            return String(val).split(',').map(v => v.trim()).filter(v => v !== '');
        },

        get activeUpperFieldsList() {
            return this.upperFields.filter(f => this.activeUpperKeys.includes(f.key));
        },

        get activeLowerFieldsList() {
            return this.lowerFields.filter(f => this.activeLowerKeys.includes(f.key));
        },

        toggleUpperField(key) {
            const idx = this.activeUpperKeys.indexOf(key);
            if (idx > -1) {
                this.activeUpperKeys.splice(idx, 1);
            } else {
                this.activeUpperKeys.push(key);
            }
        },

        toggleLowerField(key) {
            const idx = this.activeLowerKeys.indexOf(key);
            if (idx > -1) {
                this.activeLowerKeys.splice(idx, 1);
            } else {
                this.activeLowerKeys.push(key);
            }
        },

        showAllUpper() {
            this.activeUpperKeys = this.upperFields.map(f => f.key);
        },

        hideAllUpper() {
            this.activeUpperKeys = [];
        },

        showAllLower() {
            this.activeLowerKeys = this.lowerFields.map(f => f.key);
        },

        hideAllLower() {
            this.activeLowerKeys = [];
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
                    document.getElementById('swal_field_label').focus();
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
                this.upperFields.push(newField);
                this.saveFieldsToStorage();
                if (!this.activeUpperKeys.includes(key)) {
                    this.activeUpperKeys.push(key);
                }
                this.$nextTick(() => {
                    const el = document.getElementById('f_' + key);
                    if (el) el.focus();
                });
            }
        },

        async confirmRemoveUpperField(field) {
            const hasVal = this.parseValues(this.measurements[field.key]).length > 0;
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
            this.upperFields = this.upperFields.filter(f => f.key !== key);
            this.activeUpperKeys = this.activeUpperKeys.filter(k => k !== key);
            delete this.measurements[key];
            this.saveFieldsToStorage();
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
                const customFields = this.upperFields.filter(f => f.isCustom);
                const restored = this.defaultUpperFields.map(f => ({ ...f }));
                customFields.forEach(cf => {
                    if (!restored.some(r => r.key === cf.key)) restored.push(cf);
                });
                this.upperFields = restored;
                this.saveFieldsToStorage();
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
                    document.getElementById('swal_lfield_label').focus();
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
                this.lowerFields.push(newField);
                this.saveFieldsToStorage();
                if (!this.activeLowerKeys.includes(key)) {
                    this.activeLowerKeys.push(key);
                }
                this.$nextTick(() => {
                    const el = document.getElementById('f_' + key);
                    if (el) el.focus();
                });
            }
        },

        async confirmRemoveLowerField(field) {
            const hasVal = this.parseValues(this.measurements[field.key]).length > 0;
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
            this.lowerFields = this.lowerFields.filter(f => f.key !== key);
            this.activeLowerKeys = this.activeLowerKeys.filter(k => k !== key);
            delete this.measurements[key];
            this.saveFieldsToStorage();
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
                const customFields = this.lowerFields.filter(f => f.isCustom);
                const restored = this.defaultLowerFields.map(f => ({ ...f }));
                customFields.forEach(cf => {
                    if (!restored.some(r => r.key === cf.key)) restored.push(cf);
                });
                this.lowerFields = restored;
                this.saveFieldsToStorage();
            }
        },

        get upperFilledCount() {
            return this.upperFields.filter(f => this.parseValues(this.measurements[f.key]).length > 0).length;
        },

        get lowerFilledCount() {
            return this.lowerFields.filter(f => this.parseValues(this.measurements[f.key]).length > 0).length;
        },

        async init() {
            this.loadFieldsFromStorage();
            // Apply URL query params if opened from customer view e.g. ?customer_id=1
            const params = new URLSearchParams(window.location.search);
            const cid = params.get('customer_id');
            const mid = params.get('member_id');
            if (cid) {
                try {
                    const c = await (await fetch('{{ url("api/orders/customer") }}/' + cid, { headers: { 'Accept': 'application/json' } })).json();
                    this.selectCustomer(c);
                    if (mid) {
                        this.selectMember(mid);
                    }
                } catch (e) {}
            }
        },

        async openSearch() {
            if (this.selectedCustomer) return;
            this.searchOpen = true;
            if (!this.searchQuery.trim()) {
                await this.searchCustomers();
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

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.searchOpen = false;
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.memberList = Array.isArray(c.members) ? c.members : [];
            this.selectedMemberId = '__self__';
            this.searchOpen = false;
            this.searchQuery = '';
            this.loadExistingMeasurements();
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.selectedMemberId = '__self__';
            this.memberList = [];
            this.measurements = {};
            this.customFields = [];
            this.loadFieldsFromStorage();
            this.activeUpperKeys = [];
            this.activeLowerKeys = [];
            this.prevSavedDate = null;
            this.notes = '';
        },

        selectMember(mid) {
            this.selectedMemberId = mid;
            this.loadExistingMeasurements();
        },

        async loadExistingMeasurements() {
            if (!this.selectedCustomer) return;
            this.prevSavedDate = null;
            try {
                const params = new URLSearchParams();
                if (this.selectedMemberId && this.selectedMemberId !== '__self__') {
                    params.append('member_id', this.selectedMemberId);
                }
                const url = '{{ url("api/measurements/customer") }}/' + this.selectedCustomer.id + '?' + params.toString();
                const res = await (await fetch(url, { headers: { 'Accept': 'application/json' } })).json();
                if (res && res.measurements && Object.keys(res.measurements).length > 0) {
                    const data = { ...res.measurements };
                    // Extract style preferences if present
                    if (data.__style) {
                        this.stylePreferences = { ...this.stylePreferences, ...data.__style };
                        delete data.__style;
                    }
                    // Extract custom fields if present
                    if (data.__custom && Array.isArray(data.__custom)) {
                        this.customFields = data.__custom;
                        delete data.__custom;
                    }

                    // Always base off user's persistent buttons from storage
                    this.loadFieldsFromStorage();

                    // If this customer has saved custom field definitions not present in current list, include them for viewing
                    if (data.__custom_fields && Array.isArray(data.__custom_fields)) {
                        data.__custom_fields.forEach(cf => {
                            if (cf.section === 'upper' && !this.upperFields.some(f => f.key === cf.key)) {
                                this.upperFields.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
                            } else if (cf.section === 'lower' && !this.lowerFields.some(f => f.key === cf.key)) {
                                this.lowerFields.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
                            }
                        });
                        delete data.__custom_fields;
                    }

                    // Format array values into comma-separated strings for inputs and auto-open active fields
                    this.activeUpperKeys = [];
                    this.activeLowerKeys = [];

                    this.upperFields.forEach(f => {
                        if (data[f.key] !== undefined && data[f.key] !== null) {
                            if (Array.isArray(data[f.key])) {
                                data[f.key] = data[f.key].join(', ');
                            }
                            if (this.parseValues(data[f.key]).length > 0) {
                                if (!this.activeUpperKeys.includes(f.key)) {
                                    this.activeUpperKeys.push(f.key);
                                }
                            }
                        }
                    });

                    this.lowerFields.forEach(f => {
                        if (data[f.key] !== undefined && data[f.key] !== null) {
                            if (Array.isArray(data[f.key])) {
                                data[f.key] = data[f.key].join(', ');
                            }
                            if (this.parseValues(data[f.key]).length > 0) {
                                if (!this.activeLowerKeys.includes(f.key)) {
                                    this.activeLowerKeys.push(f.key);
                                }
                            }
                        }
                    });

                    this.measurements = data;
                    this.notes = res.notes || '';
                    this.prevSavedDate = res.saved_date || null;
                } else {
                    this.measurements = {};
                    this.customFields = [];
                    this.loadFieldsFromStorage();
                    this.activeUpperKeys = [];
                    this.activeLowerKeys = [];
                    this.notes = '';
                }
            } catch (e) {}
        },

        addCustomField() {
            this.customFields.push({ label: '', value: '' });
        },

        removeCustomField(idx) {
            this.customFields.splice(idx, 1);
        },

        async saveMeasurements() {
            if (!this.selectedCustomer) {
                Swal.fire({ icon: 'warning', title: 'Customer Required', text: 'Please select a customer first.', confirmButtonColor: '#6366f1' });
                return;
            }

            const filledCount = this.upperFilledCount + this.lowerFilledCount + this.customFields.filter(c => c.value.trim() !== '').length;
            if (filledCount === 0) {
                Swal.fire({ icon: 'warning', title: 'No Measurements Entered', text: 'Please enter at least one measurement value.', confirmButtonColor: '#6366f1' });
                return;
            }

            this.saving = true;

            // Prepare payload with clean arrays of multiple values
            const payloadData = {};
            for (const key of Object.keys(this.measurements)) {
                const parsed = this.parseValues(this.measurements[key]);
                if (parsed.length > 0) {
                    payloadData[key] = parsed;
                }
            }
            payloadData.__style = this.stylePreferences;
            if (this.customFields.length > 0) {
                payloadData.__custom = this.customFields.filter(c => c.label.trim() !== '');
            }

            // Save custom fields definitions so they persist across sessions
            const customUpper = this.upperFields.filter(f => f.isCustom).map(f => ({ section: 'upper', key: f.key, label: f.label, urdu: f.urdu }));
            const customLower = this.lowerFields.filter(f => f.isCustom).map(f => ({ section: 'lower', key: f.key, label: f.label, urdu: f.urdu }));
            const allCustomDefs = [...customUpper, ...customLower];
            if (allCustomDefs.length > 0) {
                payloadData.__custom_fields = allCustomDefs;
            }

            const memberIds = [this.selectedMemberId === '__self__' ? null : this.selectedMemberId];

            try {
                const response = await fetch('{{ route("measurements.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: this.selectedCustomer.id,
                        member_ids: memberIds,
                        service_id: null, // General measurements independent of service!
                        measurements: payloadData,
                        notes: this.notes
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved Successfully!',
                        text: 'General measurements have been recorded for ' + (this.selectedCustomer.name) + '.',
                        confirmButtonColor: '#6366f1'
                    }).then(() => {
                        window.location.href = '{{ route("measurements.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Save Failed',
                        text: result.message || 'Please check your inputs and try again.',
                        confirmButtonColor: '#6366f1'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not connect to server. Please try again.',
                    confirmButtonColor: '#6366f1'
                });
            }
            this.saving = false;
        },

        async saveQuickCustomer() {
            this.qcError = '';
            if (!this.qc.name.trim() || !this.qc.phone.trim()) {
                this.qcError = 'Name and Phone are required.';
                return;
            }
            this.qcSaving = true;
            try {
                const res = await (await fetch('{{ route("api.orders.quickCustomer") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.qc)
                })).json();

                if (res.id) {
                    this.selectCustomer(res);
                    this.showQuickCustomer = false;
                    this.qc = { name: '', phone: '', gender: 'male', address: '' };
                } else {
                    this.qcError = res.message || 'Failed to save customer.';
                }
            } catch (e) {
                this.qcError = 'Network error occurred.';
            }
            this.qcSaving = false;
        },

        async saveQuickMember() {
            this.qmError = '';
            if (!this.qm.name.trim()) {
                this.qmError = 'Member name is required.';
                return;
            }
            this.qmSaving = true;
            try {
                const res = await (await fetch('{{ route("api.orders.quickMember") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: this.selectedCustomer.id,
                        name: this.qm.name,
                        relation: this.qm.relation,
                        gender: this.qm.gender
                    })
                })).json();

                if (res.id) {
                    this.memberList.push(res);
                    this.selectMember(res.id);
                    this.showQuickMember = false;
                    this.qm = { name: '', relation: '', gender: 'male' };
                } else {
                    this.qmError = res.message || 'Failed to save member.';
                }
            } catch (e) {
                this.qmError = 'Network error occurred.';
            }
            this.qmSaving = false;
        }
    };
}
</script>
@endpush
