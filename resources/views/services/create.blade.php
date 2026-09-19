@extends('layouts.app')

@section('title', 'Add New Service')

@section('content')
<div class="space-y-5 max-w-7xl mx-auto" x-data="serviceForm()">

    {{-- Top Navigation & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Services
                </a>
                <span>/</span>
                <span class="text-slate-400">New Service</span>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm shadow-indigo-500/25 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-tight">Create New Service</h2>
                    <p class="text-xs text-slate-500">Configure service pricing tiers (Basic, Standard, Premium) and turnaround times.</p>
                </div>
            </div>
        </div>

        {{-- Quick Header Actions --}}
        <div class="flex items-center gap-2.5">
            <a href="{{ route('services.index') }}"
                class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-xs">
                Cancel
            </a>
            <button type="button" @click="$refs.mainForm.requestSubmit()"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-500/25 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Save Service
            </button>
        </div>
    </div>

    {{-- Error Banner --}}
    @if ($errors->any())
        <div class="bg-red-50/90 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-bold text-red-800">Please correct the following errors:</h4>
                <ul class="list-disc list-inside text-xs text-red-600 mt-0.5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Form Container (2-Column Grid) --}}
    <form method="POST" action="{{ route('services.store') }}" x-ref="mainForm" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        @csrf

        {{-- Hidden default base price (synced to Standard price) --}}
        <input type="hidden" name="price" :value="priceStandard">

        {{-- ================= LEFT COLUMN: FORM INPUTS (7 Cols) ================= --}}
        <div class="lg:col-span-7 space-y-4">

            {{-- Card 1: Basic Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-sm font-bold text-slate-800">Basic Information</h3>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium">* Required</span>
                </div>

                {{-- Service Name with Quick Suggestions --}}
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                        Service Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                            </svg>
                        </div>
                        <input type="text" id="name" name="name" x-model="name" required autofocus
                            class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="e.g. Shalwar Kameez, Pant Shirt, Coat..." value="{{ old('name') }}">
                    </div>

                    {{-- Popular Presets Pills --}}
                    <div class="mt-2 flex flex-wrap items-center gap-1.5">
                        <span class="text-[11px] font-medium text-slate-400 mr-1">Garment Presets:</span>
                        <template x-for="p in popularPresets" :key="p.name">
                            <button type="button" @click="applyPreset(p)"
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg text-xs font-medium bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 border border-slate-200/80 transition">
                                <span x-text="p.icon"></span>
                                <span x-text="p.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                            Description
                        </label>
                        <span class="text-[10px] text-slate-400">Optional · Shown in receipts &amp; catalog</span>
                    </div>
                    <textarea id="description" name="description" rows="2" x-model="description"
                        class="block w-full px-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                        placeholder="Add details about fabric, stitching style, or special inclusions...">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Card 2: Pricing Tiers (Basic, Standard, Premium) & Timeline --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">2</span>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Pricing by Type &amp; Timeline</h3>
                            <p class="text-[11px] text-slate-400">Set rates for Basic, Standard, and Premium stitching</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full font-semibold border border-indigo-100">3 Tiers</span>
                </div>

                {{-- 3-Tier Pricing Grid (Basic, Standard, Premium) --}}
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-2">
                        Stitching Type Prices (Rs.) <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        {{-- 1. Basic Tier --}}
                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition relative focus-within:border-slate-400 focus-within:ring-2 focus-within:ring-slate-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-slate-700 flex items-center gap-1">
                                    <span>🥉</span> Basic
                                </span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-slate-200 text-slate-600 font-medium">Simple</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mb-2">Regular / Simple stitch</p>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs font-bold text-slate-400">Rs</span>
                                <input type="number" name="price_basic" x-model.number="priceBasic" min="0" step="50" required
                                    class="w-full pl-8 pr-2 py-1.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-slate-500 focus:border-slate-500 transition"
                                    placeholder="1000">
                            </div>
                            <div class="mt-2 flex items-center gap-1">
                                <template x-for="q in [800, 1000, 1200]" :key="'b_'+q">
                                    <button type="button" @click="priceBasic = q"
                                        class="px-1.5 py-0.5 text-[10px] font-semibold rounded transition"
                                        :class="priceBasic === q ? 'bg-slate-700 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'"
                                        x-text="q"></button>
                                </template>
                            </div>
                        </div>

                        {{-- 2. Standard Tier (Base / Default) --}}
                        <div class="p-3.5 rounded-xl border-2 border-indigo-500 bg-indigo-50/40 relative shadow-2xs">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-indigo-900 flex items-center gap-1">
                                    <span>🥈</span> Standard
                                </span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-indigo-600 text-white font-bold tracking-wider uppercase">Default</span>
                            </div>
                            <p class="text-[10px] text-indigo-600/80 mb-2">Quality fusing &amp; finish</p>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs font-bold text-indigo-500">Rs</span>
                                <input type="number" name="price_standard" x-model.number="priceStandard" min="0" step="50" required
                                    class="w-full pl-8 pr-2 py-1.5 bg-white border border-indigo-300 rounded-lg text-sm font-bold text-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="1500">
                            </div>
                            <div class="mt-2 flex items-center gap-1">
                                <template x-for="q in [1200, 1500, 1800]" :key="'s_'+q">
                                    <button type="button" @click="priceStandard = q"
                                        class="px-1.5 py-0.5 text-[10px] font-semibold rounded transition"
                                        :class="priceStandard === q ? 'bg-indigo-600 text-white' : 'bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-100'"
                                        x-text="q"></button>
                                </template>
                            </div>
                        </div>

                        {{-- 3. Premium Tier --}}
                        <div class="p-3.5 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-50 transition relative focus-within:border-purple-400 focus-within:ring-2 focus-within:ring-purple-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-purple-900 flex items-center gap-1">
                                    <span>🥇</span> Premium
                                </span>
                                <span class="text-[10px] px-1.5 py-0.2 rounded bg-purple-200 text-purple-800 font-medium">VIP / Master</span>
                            </div>
                            <p class="text-[10px] text-purple-600/80 mb-2">Designer hand-finish</p>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-xs font-bold text-purple-500">Rs</span>
                                <input type="number" name="price_premium" x-model.number="pricePremium" min="0" step="50" required
                                    class="w-full pl-8 pr-2 py-1.5 bg-white border border-purple-300 rounded-lg text-sm font-bold text-purple-900 focus:outline-none focus:ring-1 focus:ring-purple-500 focus:border-purple-500 transition"
                                    placeholder="2500">
                            </div>
                            <div class="mt-2 flex items-center gap-1">
                                <template x-for="q in [2000, 2500, 3000]" :key="'p_'+q">
                                    <button type="button" @click="pricePremium = q"
                                        class="px-1.5 py-0.5 text-[10px] font-semibold rounded transition"
                                        :class="pricePremium === q ? 'bg-purple-600 text-white' : 'bg-white border border-purple-200 text-purple-700 hover:bg-purple-100'"
                                        x-text="q"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Estimated Days & Active Status in 2 Columns --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label for="estimated_days" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                            Estimated Stitching Time (Days)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="number" id="estimated_days" name="estimated_days" x-model.number="estimated_days" min="1" max="60"
                                class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                placeholder="e.g. 5" value="{{ old('estimated_days', 5) }}">
                        </div>

                        {{-- Quick Day Chips --}}
                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                            <span class="text-[11px] text-slate-400">Quick:</span>
                            <template x-for="qd in [3, 5, 7, 10]" :key="qd">
                                <button type="button" @click="estimated_days = qd"
                                    class="px-2 py-0.5 rounded text-[11px] font-semibold transition"
                                    :class="estimated_days === qd ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    x-text="qd + 'd'">
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Active Status Toggle --}}
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200 bg-slate-50/60 cursor-pointer select-none" @click="isActive = !isActive">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" :checked="isActive" class="hidden">
                            <div>
                                <div class="font-semibold text-xs text-slate-800 flex items-center gap-1.5">
                                    <span>Active Status</span>
                                    <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                        :class="isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'"
                                        x-text="isActive ? 'Available' : 'Hidden'">
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-0.5">Show in order booking catalog</p>
                            </div>

                            <button type="button" @click.stop="isActive = !isActive"
                                class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="isActive ? 'bg-indigo-600' : 'bg-slate-300'">
                                <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="isActive ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Bottom Actions --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                    <a href="{{ route('services.index') }}"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-500/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Save Service
                    </button>
                </div>
            </div>

        </div>

        {{-- ================= RIGHT COLUMN: INTERACTIVE LIVE PREVIEW (5 Cols) ================= --}}
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full" :class="isActive ? 'bg-emerald-400 animate-pulse' : 'bg-slate-400'"></span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Catalog Preview</span>
                    </div>
                    <span class="text-[11px] font-medium text-slate-400" x-text="isActive ? 'Active in Orders' : 'Hidden from Catalog'"></span>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Service Header --}}
                    <div class="flex items-start justify-between gap-3">
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center flex-shrink-0 text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                            :class="isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                            x-text="isActive ? 'Active' : 'Inactive'">
                        </span>
                    </div>

                    <div>
                        <h4 class="text-base font-bold text-slate-800 leading-snug" x-text="name ? name : 'Service Name'"></h4>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2" x-text="description ? description : 'No additional description provided.'"></p>
                    </div>

                    {{-- 3-Tier Pricing Preview Breakdown --}}
                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                            <span>Package Rates</span>
                            <span class="text-[11px] font-medium text-slate-400" x-text="'Delivery: ' + (estimated_days || 5) + ' Days'"></span>
                        </div>

                        <div class="space-y-2">
                            {{-- Basic --}}
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">🥉</span>
                                    <div>
                                        <span class="font-bold text-slate-700">Basic</span>
                                        <span class="text-[10px] text-slate-400 block -mt-0.5">Simple stitching</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-slate-800" x-text="'Rs. ' + Number(priceBasic || 0).toLocaleString()"></span>
                            </div>

                            {{-- Standard --}}
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-indigo-50/70 border border-indigo-200 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">🥈</span>
                                    <div>
                                        <span class="font-bold text-indigo-900">Standard</span>
                                        <span class="text-[10px] text-indigo-600/80 block -mt-0.5">Recommended (Base)</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-indigo-900" x-text="'Rs. ' + Number(priceStandard || 0).toLocaleString()"></span>
                            </div>

                            {{-- Premium --}}
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-purple-50/70 border border-purple-200 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm">🥇</span>
                                    <div>
                                        <span class="font-bold text-purple-900">Premium</span>
                                        <span class="text-[10px] text-purple-600/80 block -mt-0.5">VIP Master finish</span>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-purple-900" x-text="'Rs. ' + Number(pricePremium || 0).toLocaleString()"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pro Tip Banner --}}
                <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-[11px] text-slate-500 leading-relaxed">
                    💡 Customer measurements are recorded universally under the customer's profile, keeping service creation simple and focused on pricing packages.
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function serviceForm() {
    return {
        name: '{{ addslashes(old('name', 'Shalwar Kameez')) }}',
        description: '{{ addslashes(old('description', '')) }}',
        priceBasic: {{ (float) old('price_basic', 1000) }},
        priceStandard: {{ (float) old('price_standard', old('price', 1500)) }},
        pricePremium: {{ (float) old('price_premium', 2500) }},
        estimated_days: {{ (int) old('estimated_days', 5) }},
        isActive: {{ old('is_active', '1') == '1' ? 'true' : 'false' }},

        popularPresets: [
            { name: 'Shalwar Kameez', icon: '👕', basic: 1000, standard: 1500, premium: 2500, days: 5 },
            { name: 'Pant & Shirt', icon: '👔', basic: 1200, standard: 1800, premium: 2800, days: 6 },
            { name: 'Coat / Blazer', icon: '🧥', basic: 3500, standard: 4500, premium: 6500, days: 10 },
            { name: 'Waistcoat', icon: '🦺', basic: 1200, standard: 1800, premium: 2500, days: 4 },
            { name: 'Kurta Pajama', icon: '👘', basic: 900, standard: 1300, premium: 2000, days: 5 },
        ],

        applyPreset(p) {
            this.name = p.name;
            if (p.basic) this.priceBasic = p.basic;
            if (p.standard) this.priceStandard = p.standard;
            if (p.premium) this.pricePremium = p.premium;
            if (p.days) this.estimated_days = p.days;
        }
    };
}
</script>
@endpush
