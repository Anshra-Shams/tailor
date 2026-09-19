@extends('layouts.app')

@section('title', 'Edit Measurement #' . $measurement->id)

@section('content')
<div x-data="measurementEditor()" x-init="init()" class="space-y-6">

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
                <span class="text-slate-400">Edit #{{ $measurement->id }}</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/25">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit {{ $measurement->service ? $measurement->service->name : 'General' }} Measurements</h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Customer: <span class="font-semibold text-slate-700">{{ $measurement->customer->name ?? '—' }}</span>
                        @if($measurement->member)
                            &middot; Member: <span class="font-semibold text-slate-700">{{ $measurement->member->name }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- Top Right Actions --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('measurements.show', $measurement) }}"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-sm">
                Cancel
            </a>
            <button type="button" @click="saveChanges()" :disabled="saving"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-emerald-500/25 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!saving" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                <svg x-show="saving" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="saving ? 'Updating...' : 'Update Measurement'"></span>
            </button>
        </div>
    </div>

    @if(!$measurement->service_id)
        {{-- ================= GENERAL MEASUREMENTS EDIT FORM ================= --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 space-y-6">

                {{-- Upper Body Measurements --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="text-base font-bold text-slate-800">Upper Body Measurements</h3>
                        </div>
                        <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-full">Kameez / Shirt / Coat</span>
                    </div>                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Length (Lambai)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.length" placeholder="e.g. 38.5, 40" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Chest (Chaati)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.chest" placeholder="e.g. 40" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Waist (Kamar)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.waist" placeholder="e.g. 36" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Shoulder (Teera)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.shoulder" placeholder="e.g. 18" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Sleeves (Bazu)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.sleeves" placeholder="e.g. 24" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Collar (Gala / Neck)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.collar" placeholder="e.g. 15.5" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Daman (Ghera)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.daman" placeholder="e.g. 22.5" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Cross Back (Peeth)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.cross_back" placeholder="e.g. 17.5" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Bicep / Muscle</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.bicep" placeholder="e.g. 14" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Wrist (Cuff / Mohri)</label>
                            <div class="relative">
                                <input type="text" x-model="upperBody.wrist" placeholder="e.g. 9.5" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lower Body Measurements --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-xs">2</span>
                            <h3 class="text-base font-bold text-slate-800">Lower Body Measurements</h3>
                        </div>
                        <span class="text-xs font-semibold text-teal-600 bg-teal-50 border border-teal-100 px-2.5 py-1 rounded-full">Shalwar / Trouser / Pajama</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Trouser Length</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.length" placeholder="e.g. 39" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Waist (Belt / Nafa)</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.waist" placeholder="e.g. 34" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Hip (Seat)</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.hip" placeholder="e.g. 42" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Inseam</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.inseam" placeholder="e.g. 30" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Bottom (Paincha)</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.paincha" placeholder="e.g. 8.5" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Thigh (Raan)</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.thigh" placeholder="e.g. 26" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Crotch / Rise (Asan)</label>
                            <div class="relative">
                                <input type="text" x-model="lowerBody.asan" placeholder="e.g. 16" class="w-full pl-3 pr-8 py-2 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                <span class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none text-slate-400 text-xs font-medium">in</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Style Preferences --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xs">3</span>
                            <h3 class="text-base font-bold text-slate-800">Fitting &amp; Styling Preferences</h3>
                        </div>
                        <span class="text-xs text-slate-400">Default tailor preferences</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Fitting Style</label>
                            <select x-model="styles.fitting" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500">
                                <option value="regular">Regular Fit</option>
                                <option value="slim">Slim Fit</option>
                                <option value="loose">Loose / Relaxed</option>
                                <option value="tailored">Custom Tailored</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Collar / Neck Style</label>
                            <select x-model="styles.collar_type" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500">
                                <option value="sherwani">Sherwani Collar (Ban)</option>
                                <option value="shirt">Shirt Collar</option>
                                <option value="band">Chinese / Mandarin</option>
                                <option value="v_neck">V-Neck / Open</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Daman Cut</label>
                            <select x-model="styles.daman_type" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500">
                                <option value="round">Ghol Daman (Round)</option>
                                <option value="square">Choras Daman (Square)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Pocket Style</label>
                            <select x-model="styles.pockets" class="w-full py-2 px-3 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 focus:ring-2 focus:ring-indigo-500">
                                <option value="both">Front &amp; Side Pockets</option>
                                <option value="left">Left Chest Pocket Only</option>
                                <option value="none">No Front Pocket</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Notes & Instructions --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
                    <h3 class="text-base font-bold text-slate-800">Master Instructions / Notes</h3>
                    <textarea x-model="notes" rows="3" placeholder="Special customer instructions, posture notes, fitting remarks..."
                        class="w-full p-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"></textarea>
                </div>
            </div>

            {{-- Right Sidebar Actions & Summary --}}
            <div class="lg:col-span-4">
                <div class="lg:sticky lg:top-6 space-y-5">
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                        <h4 class="font-bold text-slate-800 text-sm">Measurement Profile Info</h4>
                        <div class="divide-y divide-slate-100 text-xs space-y-2">
                            <div class="pt-2 flex justify-between">
                                <span class="text-slate-400">Customer:</span>
                                <span class="font-semibold text-slate-700">{{ $measurement->customer->name ?? '—' }}</span>
                            </div>
                            @if($measurement->member)
                            <div class="pt-2 flex justify-between">
                                <span class="text-slate-400">Member:</span>
                                <span class="font-semibold text-slate-700">{{ $measurement->member->name }}</span>
                            </div>
                            @endif
                            <div class="pt-2 flex justify-between">
                                <span class="text-slate-400">Type:</span>
                                <span class="font-semibold text-emerald-600">General Profile</span>
                            </div>
                        </div>
                        <button type="button" @click="saveChanges()" :disabled="saving"
                            class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition shadow-md shadow-emerald-600/20 text-sm flex items-center justify-center gap-2">
                            <span x-text="saving ? 'Updating...' : 'Save & Update'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- ================= SERVICE-SPECIFIC MEASUREMENTS EDIT FORM ================= --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">{{ $measurement->service->name }} Measurements</h3>
                    <span class="text-xs px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full font-semibold border border-indigo-100">Service Bound</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if($measurement->service->measurement_fields)
                        @foreach($measurement->service->measurement_fields as $field)
                            <div>
                                <label class="block text-xs font-medium text-slate-500 mb-1">
                                    {{ $field['label'] ?? ucwords(str_replace('_', ' ', $field['key'])) }}
                                    @if(!empty($field['required'])) <span class="text-red-500">*</span> @endif
                                </label>
                                <input type="text" x-model="serviceMeasurements['{{ $field['key'] }}']" placeholder="in inches" class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-base font-bold text-slate-800 mb-2">Special Instructions</h3>
                <textarea x-model="notes" rows="2" placeholder="Any special instructions or notes..." class="w-full py-2 px-3 rounded-lg border border-slate-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none"></textarea>
            </div>
        </div>
    @endif

    {{-- HIDDEN FORM FOR ACTUAL SUBMISSION --}}
    <form id="editMeasurementForm" method="POST" action="{{ route('measurements.update', $measurement) }}" style="display:none">
        @csrf @method('PUT')
        <input type="hidden" name="customer_id"  value="{{ $measurement->customer_id }}">
        <input type="hidden" name="member_id"    value="{{ $measurement->member_id }}">
        <input type="hidden" name="service_id"   value="{{ $measurement->service_id }}">
        <input type="hidden" name="notes"        :value="notes">
        <input type="hidden" name="measurements" :value="JSON.stringify(getFinalMeasurements())">
    </form>

</div>
@endsection

@push('scripts')
<script>
function measurementEditor() {
    const rawData = @json($measurement->data ?? []);
    const isGeneral = !{{ $measurement->service_id ? 'true' : 'false' }};
    const fmt = (v) => Array.isArray(v) ? v.join(', ') : (v || '');

    return {
        saving: false,
        notes: @js($measurement->notes ?? ''),
        isGeneral: isGeneral,
        
        // General state
        upperBody: {
            length: fmt(rawData.kameez_length || rawData.length),
            chest: fmt(rawData.chest),
            waist: fmt(rawData.waist_upper || rawData.waist),
            shoulder: fmt(rawData.shoulder),
            sleeves: fmt(rawData.sleeves),
            collar: fmt(rawData.collar),
            daman: fmt(rawData.daman),
            cross_back: fmt(rawData.cross_back),
            bicep: fmt(rawData.bicep),
            wrist: fmt(rawData.wrist)
        },
        lowerBody: {
            length: fmt(rawData.shalwar_length || rawData.trouser_length || rawData.lower_length),
            waist: fmt(rawData.waist_lower || rawData.trouser_waist || rawData.lower_waist),
            hip: fmt(rawData.hip),
            inseam: fmt(rawData.inseam),
            paincha: fmt(rawData.paincha),
            thigh: fmt(rawData.thigh),
            asan: fmt(rawData.asan)
        },
        styles: {
            fitting: rawData.__style?.fitting || 'regular',
            collar_type: rawData.__style?.collar_type || 'sherwani',
            daman_type: rawData.__style?.daman_type || 'round',
            pockets: rawData.__style?.pockets || 'both'
        },

        // Service-specific state
        serviceMeasurements: { ...rawData },

        init() {},

        getFinalMeasurements() {
            if (this.isGeneral) {
                const parse = (v) => {
                    if (Array.isArray(v)) return v;
                    return String(v || '').split(',').map(s => s.trim()).filter(s => s !== '');
                };
                const result = {};
                for (const [k, v] of Object.entries(this.upperBody)) {
                    const p = parse(v);
                    if (p.length > 0) {
                        if (k === 'length') result['kameez_length'] = p;
                        else if (k === 'waist') result['waist_upper'] = p;
                        else result[k] = p;
                    }
                }
                for (const [k, v] of Object.entries(this.lowerBody)) {
                    const p = parse(v);
                    if (p.length > 0) {
                        if (k === 'length') result['shalwar_length'] = p;
                        else if (k === 'waist') result['waist_lower'] = p;
                        else result[k] = p;
                    }
                }
                result.__style = { ...this.styles };
                return result;
            }
            return this.serviceMeasurements;
        },

        async saveChanges() {
            this.saving = true;
            await new Promise(r => setTimeout(r, 50));
            document.getElementById('editMeasurementForm').submit();
        }
    };
}
</script>
@endpush
