@extends('layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-indigo-500 to-cyan-500 text-white shadow-lg shadow-indigo-500/25">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <span>Company & System Settings</span>
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Manage your shop profile, sidebar branding, contact details, and invoice terms.
            </p>
        </div>
    </div>

    {{-- Main Settings Form Card --}}
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Branding & Company Logo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Branding & Logo</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Upload your company logo for sidebar branding.
                </p>
            </div>

            <div x-data="{
                    preview: '{{ $setting->logo_url }}',
                    removeLogo: false,
                    onFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.preview = URL.createObjectURL(file);
                            this.removeLogo = false;
                        }
                    }
                 }" class="flex flex-col md:flex-row items-start md:items-center gap-6">

                {{-- Preview Box --}}
                <div class="relative w-32 h-32 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0 shadow-inner group">
                    <template x-if="preview && !removeLogo">
                        <img :src="preview" alt="Company Logo" class="w-full h-full object-contain p-2" />
                    </template>
                    <template x-if="!preview || removeLogo">
                        <div class="text-center p-3 text-slate-400">
                            <svg class="w-10 h-10 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[11px] font-medium block mt-1">No Logo</span>
                        </div>
                    </template>
                </div>

                {{-- Upload & Controls --}}
                <div class="flex-1 space-y-3">
                    <label class="block text-sm font-semibold text-slate-700">Company Logo (PNG, JPG, SVG, WEBP)</label>
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-semibold cursor-pointer transition shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <span>Choose New Logo</span>
                            <input type="file" name="company_logo" accept="image/*" class="hidden" @change="onFileChange" />
                        </label>

                        @if($setting->company_logo)
                        <button type="button" 
                                @click="removeLogo = !removeLogo" 
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium rounded-xl border transition"
                                :class="removeLogo ? 'bg-amber-50 text-amber-700 border-amber-300' : 'bg-red-50 text-red-600 border-red-200 hover:bg-red-100'">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span x-text="removeLogo ? 'Will Be Removed on Save (Undo)' : 'Remove Current Logo'"></span>
                        </button>
                        @endif
                    </div>
                    <input type="hidden" name="remove_logo" :value="removeLogo ? '1' : '0'">

                    @error('company_logo')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Company Profile & Contact Information --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Company & Invoice Header Details</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    These details will appear in the invoice header and system sidebar.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Company Name --}}
                <div class="space-y-1.5">
                    <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Company / Shop Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="company_name" 
                               id="company_name" 
                               value="{{ old('company_name', $setting->company_name) }}"
                               placeholder="e.g. Meco" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium text-slate-800"
                               required />
                    </div>
                    @error('company_name')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="space-y-1.5">
                    <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Phone Number(s)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               value="{{ old('phone', $setting->phone) }}"
                               placeholder="e.g. 0223-860680 - 0223862252" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium text-slate-800" />
                    </div>
                    @error('phone')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Website Link --}}
                <div class="space-y-1.5 md:col-span-2">
                    <label for="website_link" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Website Link / Social Link
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <input type="text" 
                               name="website_link" 
                               id="website_link" 
                               value="{{ old('website_link', $setting->website_link) }}"
                               placeholder="e.g. www.Meco.Com.Pk" 
                               class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium text-slate-800" />
                    </div>
                    @error('website_link')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="space-y-1.5 md:col-span-2">
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Shop / Business Address
                    </label>
                    <div class="relative">
                        <textarea name="address" 
                                  id="address" 
                                  rows="2"
                                  placeholder="e.g. SHOP # 5 & 6 RED CRESCENT HOSPITAL MARKET LATIFABAD # 6, HYDERABAD" 
                                  class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium text-slate-800">{{ old('address', $setting->address) }}</textarea>
                    </div>
                    @error('address')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Invoice Terms & Conditions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Invoice Terms &amp; Conditions (Notes)</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    Har nayi line (Enter press kar k) invoice par alag bullet point banegi.
                </p>
            </div>

            <div class="space-y-1.5">
                <textarea name="invoice_terms" 
                          id="invoice_terms" 
                          rows="4"
                          placeholder="Due Electricity failure delivery can be delay&#10;After one month of Deliver date we wouldn't accept any claim"
                          class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition font-medium text-slate-800 leading-relaxed">{{ old('invoice_terms', $setting->invoice_terms) }}</textarea>
                <p class="text-[11px] text-slate-400">
                    💡 Example: Har rule/shart ko nayi line par likhein. Yeh invoice ke bottom "Notes / Terms" box ma bullets ke tor par print hongi.
                </p>
                @error('invoice_terms')
                    <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('dashboard') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                Cancel
            </a>
            <button type="submit" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-500/30 transition transform hover:-translate-y-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Save Settings</span>
            </button>
        </div>
    </form>

</div>
@endsection
