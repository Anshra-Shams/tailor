@extends('layouts.app')

@section('title', 'Edit Measurement #' . $measurement->id)

@section('content')
<div x-data="measurementEditor()" class="space-y-6">

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
                <a href="{{ route('measurements.show', $measurement) }}" class="text-slate-500 hover:text-slate-700 transition">Profile #{{ $measurement->id }}</a>
                <span>/</span>
                <span class="text-slate-400">Edit</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Customer Measurements</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Update master body measurements for <strong class="text-slate-700">{{ $measurement->customer->name ?? 'Customer' }}</strong>. These measurements apply across all tailoring orders.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main 2-Column Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ================= LEFT COLUMN: FORM (8 Cols) ================= --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Card 1: Customer & Member Profile --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-base font-bold text-slate-800">Customer &amp; Member Profile</h3>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-full">
                        Profile #{{ $measurement->id }}
                    </span>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200/80 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-base flex items-center justify-center flex-shrink-0 shadow-sm">
                                {{ strtoupper(substr($measurement->customer->name ?? 'C', 0, 1)) }}
                            </span>
                            <div>
                                <h4 class="font-bold text-slate-800 text-base leading-tight">{{ $measurement->customer->name ?? '—' }}</h4>
                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                    <span class="font-medium">{{ $measurement->customer->phone ?? '—' }}</span>
                                    @if($measurement->customer->gender)
                                        <span class="capitalize px-2 py-0.2 rounded-full bg-slate-200/80 text-slate-600 text-[11px]">{{ $measurement->customer->gender }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Member Selection Pills --}}
                    @if($measurement->customer && $measurement->customer->members && $measurement->customer->members->count() > 0)
                        <div class="mt-4 pt-3 border-t border-slate-200/80">
                            <span class="text-xs font-bold text-slate-700 block mb-2">Record Measurements For:</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" @click="selectedMemberId = '__self__'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                                    :class="selectedMemberId === '__self__'
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20'
                                        : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                    <svg x-show="selectedMemberId === '__self__'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    <span>Self ({{ $measurement->customer->name }})</span>
                                </button>
                                @foreach($measurement->customer->members as $m)
                                    <button type="button" @click="selectedMemberId = '{{ $m->id }}'"
                                        class="px-3 py-1.5 rounded-xl border text-xs font-semibold transition flex items-center gap-1.5 shadow-xs cursor-pointer"
                                        :class="selectedMemberId === '{{ $m->id }}'
                                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20'
                                            : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'">
                                        <svg x-show="selectedMemberId === '{{ $m->id }}'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                        <span>{{ $m->name }}</span>
                                        @if($m->relation)
                                            <span class="opacity-75 text-[11px]">({{ $m->relation }})</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mt-3 flex items-center gap-2 p-2.5 rounded-lg bg-indigo-50/80 border border-indigo-100 text-xs text-indigo-700">
                        <svg class="w-4 h-4 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Created on <strong>{{ $measurement->created_at->format('M d, Y') }}</strong> &middot; Last updated <strong>{{ $measurement->updated_at->diffForHumans() }}</strong></span>
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
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border transition shadow-2xs cursor-pointer">
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
                                        class="text-slate-400 hover:text-red-500 p-0.5 rounded-md hover:bg-red-50 transition cursor-pointer"
                                        :title="'Remove ' + f.label + ' button'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    <button type="button" @click="toggleUpperField(f.key)"
                                        class="text-slate-400 hover:text-slate-600 p-0.5 rounded-md hover:bg-slate-200 transition cursor-pointer"
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
                        <button type="button" @click="showAllUpper()" class="text-indigo-600 font-semibold hover:underline cursor-pointer">Show All</button>.
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
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-xl border transition shadow-2xs cursor-pointer">
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
                                        class="text-slate-400 hover:text-red-500 p-0.5 rounded-md hover:bg-red-50 transition cursor-pointer"
                                        :title="'Remove ' + f.label + ' button'">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                    </button>
                                    <button type="button" @click="toggleLowerField(f.key)"
                                        class="text-slate-400 hover:text-slate-600 p-0.5 rounded-md hover:bg-slate-200 transition cursor-pointer"
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
                        <button type="button" @click="showAllLower()" class="text-emerald-600 font-semibold hover:underline cursor-pointer">Show All</button>.
                    </p>
                </div>
            </div>



            {{-- Bottom Save Actions --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('measurements.index') }}"
                    class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-sm">
                    Cancel
                </a>
                <button type="button" @click="saveChanges()" :disabled="saving"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-500/25 hover:shadow-emerald-500/35 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="saving ? 'Updating...' : 'Update Measurements'"></span>
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
                        <span class="text-[11px] font-medium text-slate-400">#{{ $measurement->id }}</span>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Customer info --}}
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Customer</span>
                            <h4 class="text-base font-bold text-slate-800 mt-0.5">{{ $measurement->customer->name ?? '—' }}</h4>
                            <p class="text-xs text-slate-500">{{ $measurement->customer->phone ?? '—' }}</p>
                            <p x-show="selectedMemberName" class="text-xs text-indigo-600 font-semibold mt-1">
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
                                    :style="'width: ' + (((upperFilledCount + lowerFilledCount) / (upperFields.length + lowerFields.length || 1)) * 100) + '%'"></div>
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



                        {{-- Action Buttons --}}
                        <div class="pt-3 border-t border-slate-100">
                            <button type="button" @click="saveChanges()" :disabled="saving"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-md shadow-emerald-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed text-sm cursor-pointer">
                                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                <span x-text="saving ? 'Updating...' : 'Update Measurements'"></span>
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
                            <h5 class="text-xs font-bold text-indigo-900">Live Updating</h5>
                            <p class="text-xs text-indigo-700/90 mt-0.5 leading-relaxed">
                                Editing measurements here directly updates the customer profile. Any new tailoring order created for this customer will immediately use these updated values!
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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

function measurementEditor() {
    const rawData = @json($measurement->data ?? []);
    const memberList = @json($measurement->customer->members ?? []);
    const customer = @json($measurement->customer ?? []);
    const measurementId = {{ $measurement->id }};
    const customerId = {{ $measurement->customer_id }};
    const initialMemberId = @js($measurement->member_id ? (string)$measurement->member_id : '__self__');
    const initialNotes = @js($measurement->notes ?? '');
    const serviceId = @js($measurement->service_id);

    // Helper to format values
    const parseValues = (val) => {
        if (val === undefined || val === null) return [];
        if (Array.isArray(val)) {
            return val.map(v => String(v).trim()).filter(v => v !== '');
        }
        return String(val).split(',').map(v => v.trim()).filter(v => v !== '');
    };

    // Initial fields from localStorage
    let upperList = getStoredMeasurementFields('tailor_upper_fields', DEFAULT_UPPER_FIELDS);
    let lowerList = getStoredMeasurementFields('tailor_lower_fields', DEFAULT_LOWER_FIELDS);

    // Merge custom field definitions from saved record
    if (rawData.__custom_fields && Array.isArray(rawData.__custom_fields)) {
        rawData.__custom_fields.forEach(cf => {
            if (cf.section === 'upper' && !upperList.some(f => f.key === cf.key)) {
                upperList.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
            } else if (cf.section === 'lower' && !lowerList.some(f => f.key === cf.key)) {
                lowerList.push({ key: cf.key, label: cf.label, urdu: cf.urdu || cf.label, isCustom: true });
            }
        });
    }

    // Check if any keys with values in rawData need to be in upper or lower list
    Object.keys(rawData).forEach(k => {
        if (k.startsWith('__')) return;
        const vals = parseValues(rawData[k]);
        if (vals.length > 0) {
            const inUpper = upperList.some(f => f.key === k);
            const inLower = lowerList.some(f => f.key === k);
            if (!inUpper && !inLower) {
                // Check if it matches default fields
                const defUpper = DEFAULT_UPPER_FIELDS.find(f => f.key === k);
                const defLower = DEFAULT_LOWER_FIELDS.find(f => f.key === k);
                if (defUpper) {
                    upperList.push({ ...defUpper });
                } else if (defLower) {
                    lowerList.push({ ...defLower });
                } else if (k.startsWith('custom_l_')) {
                    const cleanLabel = k.replace(/^custom_l_/, '').replace(/_\d+$/, '').replace(/_/g, ' ');
                    lowerList.push({ key: k, label: cleanLabel.charAt(0).toUpperCase() + cleanLabel.slice(1), urdu: cleanLabel, isCustom: true });
                } else {
                    const cleanLabel = k.replace(/^custom_u_/, '').replace(/_\d+$/, '').replace(/_/g, ' ');
                    upperList.push({ key: k, label: cleanLabel.charAt(0).toUpperCase() + cleanLabel.slice(1), urdu: cleanLabel, isCustom: true });
                }
            }
        }
    });

    // Populate initial measurements dictionary
    const initialMeasurements = {};
    const initialActiveUpper = [];
    const initialActiveLower = [];

    upperList.forEach(f => {
        if (rawData[f.key] !== undefined && rawData[f.key] !== null) {
            initialMeasurements[f.key] = Array.isArray(rawData[f.key]) ? rawData[f.key].join(', ') : String(rawData[f.key]);
            if (parseValues(initialMeasurements[f.key]).length > 0) {
                initialActiveUpper.push(f.key);
            }
        }
    });

    lowerList.forEach(f => {
        if (rawData[f.key] !== undefined && rawData[f.key] !== null) {
            initialMeasurements[f.key] = Array.isArray(rawData[f.key]) ? rawData[f.key].join(', ') : String(rawData[f.key]);
            if (parseValues(initialMeasurements[f.key]).length > 0) {
                initialActiveLower.push(f.key);
            }
        }
    });

    // Initial style preferences
    const initialStyle = {
        fitting: rawData.__style?.fitting || 'Regular Fit',
        collar: rawData.__style?.collar || 'Shirt Collar',
        daman: rawData.__style?.daman || 'Choras',
        pocket: rawData.__style?.pocket || 'Front Pocket + 2 Side'
    };

    // Initial custom fields
    const initialCustom = (rawData.__custom && Array.isArray(rawData.__custom)) ? rawData.__custom : [];

    return {
        unit: 'in',
        saving: false,
        measurementId: measurementId,
        customerId: customerId,
        selectedMemberId: initialMemberId,
        memberList: memberList,
        customer: customer,
        notes: initialNotes,
        serviceId: serviceId,

        defaultUpperFields: DEFAULT_UPPER_FIELDS,
        defaultLowerFields: DEFAULT_LOWER_FIELDS,

        upperFields: upperList,
        lowerFields: lowerList,

        activeUpperKeys: initialActiveUpper,
        activeLowerKeys: initialActiveLower,

        measurements: initialMeasurements,
        stylePreferences: initialStyle,
        customFields: initialCustom,

        get selectedMemberName() {
            if (this.selectedMemberId === '__self__') {
                return 'Self (' + (this.customer?.name || '') + ')';
            }
            const m = this.memberList.find(i => String(i.id) === String(this.selectedMemberId));
            return m ? (m.name + (m.relation ? ' (' + m.relation + ')' : '')) : 'Self';
        },

        parseValues: parseValues,

        get activeUpperFieldsList() {
            return this.upperFields.filter(f => this.activeUpperKeys.includes(f.key));
        },

        get activeLowerFieldsList() {
            return this.lowerFields.filter(f => this.activeLowerKeys.includes(f.key));
        },

        get upperFilledCount() {
            return this.upperFields.filter(f => this.parseValues(this.measurements[f.key]).length > 0).length;
        },

        get lowerFilledCount() {
            return this.lowerFields.filter(f => this.parseValues(this.measurements[f.key]).length > 0).length;
        },

        saveFieldsToStorage() {
            try {
                localStorage.setItem('tailor_upper_fields', JSON.stringify(this.upperFields));
                localStorage.setItem('tailor_lower_fields', JSON.stringify(this.lowerFields));
            } catch (e) {
                console.error('Failed to save measurement fields to localStorage', e);
            }
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
                customClass: { popup: 'rounded-2xl shadow-2xl' },
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
                customClass: { popup: 'rounded-2xl shadow-2xl' },
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

        addCustomField() {
            this.customFields.push({ label: '', value: '' });
        },

        removeCustomField(idx) {
            this.customFields.splice(idx, 1);
        },

        async saveChanges() {
            const filledCount = this.upperFilledCount + this.lowerFilledCount + this.customFields.filter(c => c.value.trim() !== '').length;
            if (filledCount === 0) {
                Swal.fire({ icon: 'warning', title: 'No Measurements Entered', text: 'Please enter at least one measurement value.', confirmButtonColor: '#6366f1' });
                return;
            }

            this.saving = true;

            const payloadData = {};
            for (const key of Object.keys(this.measurements)) {
                const parsed = this.parseValues(this.measurements[key]);
                if (parsed.length > 0) {
                    payloadData[key] = parsed;
                }
            }

            if (this.customFields.length > 0) {
                payloadData.__custom = this.customFields.filter(c => c.label.trim() !== '');
            }

            // Save custom fields definitions
            const customUpper = this.upperFields.filter(f => f.isCustom).map(f => ({ section: 'upper', key: f.key, label: f.label, urdu: f.urdu }));
            const customLower = this.lowerFields.filter(f => f.isCustom).map(f => ({ section: 'lower', key: f.key, label: f.label, urdu: f.urdu }));
            const allCustomDefs = [...customUpper, ...customLower];
            if (allCustomDefs.length > 0) {
                payloadData.__custom_fields = allCustomDefs;
            }

            const memberId = this.selectedMemberId === '__self__' ? null : this.selectedMemberId;

            try {
                const response = await fetch('{{ route("measurements.update", $measurement) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        customer_id: this.customerId,
                        member_id: memberId,
                        service_id: this.serviceId,
                        measurements: payloadData,
                        notes: this.notes
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Successfully!',
                        text: result.message || 'Customer measurements have been updated.',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        window.location.href = '{{ route("measurements.index") }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
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
        }
    };
}
</script>
@endpush
