@extends('layouts.app')

@section('title', 'Assign Stitching Workers - Step 3 of 3')

@section('back_button')
<button type="button" @click="$refs.backForm.submit()"
   class="group relative inline-flex items-center justify-center w-8 h-8 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 transition mr-1"
   aria-label="Back to Cutting Assignment">
    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
    </svg>
    <span class="absolute left-1/2 -translate-x-1/2 top-full mt-1.5 px-2 py-0.5 text-[10px] font-semibold text-white bg-slate-800 rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
        Back to Cutting Assignment
    </span>
</button>
@endsection

@section('content')
<div class="space-y-4" x-data="stitchingAssignmentWizard()">

    {{-- Hidden form to go back to step 2 while preserving cutting selections --}}
    <form x-ref="backForm" method="POST" action="{{ route('assign-orders.cutting') }}" class="hidden">
        @csrf
        @foreach ($orders as $index => $order)
            <input type="hidden" name="assignments[{{ $index }}][order_id]" value="{{ $order->id }}">
            <input type="hidden" name="assignments[{{ $index }}][cutting_employee_id]" value="{{ $cuttingMap[$order->id] ?? $order->cutting_employee_id }}">
        @endforeach
    </form>

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md bg-emerald-100 text-emerald-700 text-[11px] font-bold uppercase tracking-wider">Step 3 of 3</span>
                <h2 class="text-xl font-bold text-slate-800">Assign Stitching Workers (سلائی کاریگر)</h2>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Assign stitching tailors for {{ count($orders) }} selected order(s) and save assignments.</p>
        </div>

        <button type="button" @click="$refs.backForm.submit()" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 font-semibold text-xs rounded-xl transition cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Back to Cutting
        </button>
    </div>

    {{-- Quick Apply to All Bar (when multiple orders are selected) --}}
    @if (count($orders) > 1)
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white px-4 py-3 rounded-2xl shadow-md border border-teal-900/60 overflow-x-auto">
        <div class="flex items-center justify-between gap-4 min-w-[680px]">
            {{-- Left: Title & Subtitle --}}
            <div class="flex items-center gap-2.5 shrink-0">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-xl bg-emerald-400/20 text-emerald-400 font-black text-sm shrink-0">⚡</span>
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider whitespace-nowrap">
                        Quick Apply: Set Same Stitching Worker for All {{ count($orders) }} Orders
                    </h4>
                    <p class="text-[11px] text-slate-400 whitespace-nowrap">One-click shortcut to assign the same tailor to all selected orders</p>
                </div>
            </div>

            {{-- Right: Select Dropdown + Apply Button --}}
            <div class="flex items-center gap-2 shrink-0 ml-auto">
                <select x-model="bulkStitchingId" class="w-64 sm:w-72 py-1.5 px-3 bg-slate-800 border border-slate-700 text-white text-xs font-medium rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-400 shadow-inner">
                    <option value="" class="bg-white text-slate-800">-- Select Stitching Tailor for All --</option>
                    @foreach ($stitchingEmployees as $emp)
                        <option value="{{ $emp->id }}" class="bg-white text-slate-800">{{ $emp->name }} @if($emp->designation) ({{ $emp->designation->name }}) @endif</option>
                    @endforeach
                </select>

                <button type="button" @click="applyBulkStitching()" class="px-4 py-1.5 bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs rounded-xl shadow-md transition transform active:scale-95 whitespace-nowrap shrink-0 cursor-pointer">
                    Apply to All
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Helper function to format & categorize order measurements --}}
    @php
        if (!function_exists('parseCategorizedMeasurements')) {
            function parseCategorizedMeasurements($raw) {
                $upper = [];
                $lower = [];
                $other = [];
                $text = '';

                if (empty($raw)) return ['upper' => [], 'lower' => [], 'other' => [], 'text' => ''];

                if (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $raw = $decoded;
                    } else {
                        return ['upper' => [], 'lower' => [], 'other' => [], 'text' => trim($raw)];
                    }
                }

                if (!is_array($raw)) return ['upper' => [], 'lower' => [], 'other' => [], 'text' => ''];

                $lowerKeys = [
                    'shalwar_length', 'trouser_length', 'pant_length', 'length_lower', 'waist_lower',
                    'hip', 'seat', 'inseam', 'outseam', 'paincha', 'bottom', 'mori', 'thigh',
                    'asan', 'fly', 'crotch', 'knee', 'calf', 'leg_opening'
                ];

                $upperKeys = [
                    'kameez_length', 'shirt_length', 'coat_length', 'sherwani_length', 'length_upper',
                    'chest', 'bust', 'underbust', 'waist_upper', 'shoulder', 'tera', 'sleeves',
                    'collar', 'neck', 'daman', 'ghera', 'cross_back', 'bicep', 'wrist', 'cuff',
                    'armhole', 'front_neck', 'back_neck', 'yoke', 'lapel'
                ];

                foreach ($raw as $key => $val) {
                    if ($key === '__custom_fields' || str_starts_with($key, 'custom_fields_') || $key === '__style' || str_starts_with($key, 'style_')) {
                        continue;
                    }

                    if ($key === '__custom' && is_array($val)) {
                        foreach ($val as $cf) {
                            if (!empty($cf['label']) && isset($cf['value']) && is_scalar($cf['value']) && trim((string)$cf['value']) !== '') {
                                $label = trim((string)$cf['label']);
                                $valStr = (string)$cf['value'];
                                $lKey = strtolower($label);
                                if (str_contains($lKey, 'pant') || str_contains($lKey, 'shalwar') || str_contains($lKey, 'trouser') || str_contains($lKey, 'lower') || str_contains($lKey, 'paincha') || str_contains($lKey, 'asan') || str_contains($lKey, 'hip') || str_contains($lKey, 'thigh') || str_contains($lKey, 'inseam') || str_contains($lKey, 'mori')) {
                                    $lower[$label] = $valStr;
                                } else {
                                    $upper[$label] = $valStr;
                                }
                            }
                        }
                        continue;
                    }

                    $cleanVal = '';
                    if (is_scalar($val) && trim((string)$val) !== '') {
                        $cleanVal = (string) $val;
                    } elseif (is_array($val)) {
                        $flatVals = array_filter($val, fn($v) => is_scalar($v) && trim((string)$v) !== '');
                        if (!empty($flatVals)) {
                            $cleanVal = implode(', ', $flatVals);
                        }
                    }

                    if ($cleanVal === '') continue;

                    $origKey = (string) $key;
                    $lowKey = strtolower($origKey);

                    $cleanLabel = ucwords(str_replace(['_', 'custom_u_', 'custom_l_'], [' ', '', ''], $origKey));
                    $cleanLabel = preg_replace('/\s+\d+$/', '', $cleanLabel);

                    if ($lowKey === 'waist_upper' || $lowKey === 'waist_lower') {
                        $cleanLabel = 'Waist';
                    } elseif ($lowKey === 'kameez_length' || $lowKey === 'shalwar_length' || $lowKey === 'length_upper' || $lowKey === 'length_lower') {
                        $cleanLabel = 'Length';
                    }

                    if (str_starts_with($lowKey, 'custom_l_') || str_contains($lowKey, 'lower') || in_array($lowKey, $lowerKeys) || str_contains($lowKey, 'shalwar') || str_contains($lowKey, 'trouser') || str_contains($lowKey, 'pant') || str_contains($lowKey, 'paincha') || str_contains($lowKey, 'asan') || str_contains($lowKey, 'inseam') || str_contains($lowKey, 'hip') || str_contains($lowKey, 'thigh') || str_contains($lowKey, 'mori')) {
                        $lower[$cleanLabel] = $cleanVal;
                    } elseif (str_starts_with($lowKey, 'custom_u_') || str_contains($lowKey, 'upper') || in_array($lowKey, $upperKeys) || str_contains($lowKey, 'kameez') || str_contains($lowKey, 'shirt') || str_contains($lowKey, 'chest') || str_contains($lowKey, 'shoulder') || str_contains($lowKey, 'sleeves') || str_contains($lowKey, 'collar') || str_contains($lowKey, 'daman') || str_contains($lowKey, 'bicep') || str_contains($lowKey, 'wrist') || str_contains($lowKey, 'cuff') || str_contains($lowKey, 'tera')) {
                        $upper[$cleanLabel] = $cleanVal;
                    } else {
                        if (str_contains($lowKey, 'length')) {
                            $upper[$cleanLabel] = $cleanVal;
                        } else {
                            $other[$cleanLabel] = $cleanVal;
                        }
                    }
                }

                return ['upper' => $upper, 'lower' => $lower, 'other' => $other, 'text' => $text];
            }
        }
    @endphp

    {{-- Form for Step 3 Final Save Submission --}}
    <form method="POST" action="{{ route('assign-orders.save') }}" class="space-y-4">
        @csrf

        {{-- 2 Cards Per Row Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($orders as $index => $order)
                @php
                    $overdue = $order->status === 'pending' && $order->due_date && $order->due_date->isPast();
                    $parsedMeas = parseCategorizedMeasurements($order->measurements);
                    $assignedCutterId = $cuttingMap[$order->id] ?? $order->cutting_employee_id;
                    $assignedCutter = $employees->firstWhere('id', $assignedCutterId);
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    <input type="hidden" name="assignments[{{ $index }}][order_id]" value="{{ $order->id }}">
                    {{-- Cutting worker ID preserved from Step 2 --}}
                    <input type="hidden" name="assignments[{{ $index }}][cutting_employee_id]" value="{{ $assignedCutterId }}">

                    {{-- Top Header Section --}}
                    <div class="p-3.5 bg-gradient-to-b from-slate-50 to-white border-b border-slate-100 space-y-2">
                        {{-- Row 1: Order # + Customer + Invoice Link --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-white font-bold text-xs tracking-wide shadow-sm">
                                    #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="font-bold text-slate-800 text-sm">
                                    {{ $order->customer?->name ?? 'Walk-in Customer' }}
                                </span>
                                @if($order->customer?->phone)
                                    <span class="text-xs text-slate-400 font-medium">({{ $order->customer->phone }})</span>
                                @endif
                                <span class="text-[10px] font-semibold text-indigo-700 bg-indigo-50 border border-indigo-100 px-1.5 py-0.5 rounded-md">
                                    @if ($order->member)
                                        {{ $order->member->name }}
                                    @else
                                        Self
                                    @endif
                                </span>
                            </div>

                            <a href="{{ route('orders.invoice', $order) }}" target="_blank"
                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50/60 hover:bg-indigo-100/80 px-2 py-1 rounded-lg border border-indigo-100 transition whitespace-nowrap">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                <span>Invoice</span>
                            </a>
                        </div>

                        {{-- Row 2: Service, Quantity, Amount, Delivery Date --}}
                        <div class="flex items-center justify-between gap-2 flex-wrap text-xs pt-1 border-t border-slate-100/80">
                            <div class="flex items-center gap-2 text-slate-600">
                                <span class="font-semibold text-slate-800">{{ $order->service?->name ?? 'Custom Tailoring' }}</span>
                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $order->quantity }} Pc{{ $order->quantity > 1 ? 's' : '' }}</span>
                                <span class="text-slate-300">&bull;</span>
                                <span class="font-bold text-emerald-600">Rs. {{ number_format((float)$order->price * (int)$order->quantity) }}</span>
                            </div>

                            <div class="flex items-center gap-1.5 text-[11px]">
                                <span class="text-slate-400">Due:</span>
                                <span class="font-bold {{ $overdue ? 'text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100' : 'text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded' }}">
                                    {{ $order->due_date ? $order->due_date->format('d M Y') : 'Not Set' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Card Middle: Measurements, Notes & Assigned Cutting Worker Summary --}}
                    <div class="p-3.5 space-y-3 flex-1 flex flex-col justify-between">
                        <div class="space-y-2">
                            {{-- Row: Meas Button, Note (Center), Order Date (Right) --}}
                            <div class="flex items-center justify-between gap-2">
                                {{-- Left: Meas Button --}}
                                <button type="button" @click="activeModalOrder = {{ $order->id }}"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 hover:text-indigo-800 rounded-lg text-xs font-semibold border border-indigo-200/70 transition shadow-xs cursor-pointer flex-shrink-0">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                                    <span>Meas</span>
                                </button>

                                {{-- Center: Note (if present) --}}
                                @if (!empty($order->notes) && is_scalar($order->notes))
                                    <div class="flex-1 min-w-0 bg-amber-50/80 border border-amber-200/80 rounded-lg px-2.5 py-1 text-xs text-amber-900 flex items-center gap-1.5 overflow-hidden">
                                        <span class="font-bold text-amber-700 uppercase tracking-wide text-[10px] flex-shrink-0">Note:</span>
                                        <span class="font-medium truncate text-[11px]" title="{{ (string) $order->notes }}">{{ (string) $order->notes }}</span>
                                    </div>
                                @else
                                    <div class="flex-1"></div>
                                @endif

                                {{-- Right: Date --}}
                                <span class="text-[11px] text-slate-400 font-medium flex-shrink-0">
                                    {{ ($order->order_date ?? $order->created_at)?->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        {{-- Worker Assignments Section: Stitching Only --}}
                        <div class="pt-2 border-t border-slate-100">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1 flex items-center gap-1.5">
                                    <span class="text-sm">🧵</span>
                                    <span>Assign Stitching Worker</span>
                                </label>
                                <select name="assignments[{{ $index }}][stitching_employee_id]"
                                        x-model="stitchingAssignments[{{ $order->id }}]"
                                        class="block w-full py-2 px-3 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 hover:border-slate-400 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 shadow-xs transition">
                                    <option value="">-- Choose Stitching Tailor --</option>
                                    @foreach ($stitchingEmployees as $emp)
                                        <option value="{{ $emp->id }}">
                                            {{ $emp->name }} @if($emp->designation) ({{ $emp->designation->name }}) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Measurements Modal for Order #{{ $order->id }} --}}
                <div x-show="activeModalOrder === {{ $order->id }}" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                     @keydown.escape.window="activeModalOrder = null">

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full max-h-[85vh] flex flex-col overflow-hidden"
                         @click.outside="activeModalOrder = null">

                        {{-- Modal Header --}}
                        <div class="px-5 py-3.5 bg-slate-900 text-white flex items-center justify-between border-b border-slate-700 flex-shrink-0">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-indigo-500 text-white flex items-center justify-center font-bold text-xs">
                                    #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                <div>
                                    <h3 class="font-bold text-sm text-white">Measurement Details</h3>
                                    <p class="text-[11px] text-slate-300">
                                        {{ $order->customer?->name ?? 'Customer' }} &middot; {{ $order->service?->name ?? 'Service' }}
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="activeModalOrder = null" class="p-1 text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-5 overflow-y-auto space-y-4 flex-1">
                            {{-- Upper Body Measurements --}}
                            @if (count($parsedMeas['upper']) > 0)
                                <div>
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        Upper Body Measurements
                                    </h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach ($parsedMeas['upper'] as $key => $val)
                                            <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-2.5">
                                                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">{{ (string)$key }}</div>
                                                <div class="text-sm font-extrabold text-slate-800 mt-0.5">
                                                    {{ (string)$val }} <span class="text-[10px] font-medium text-slate-400">in</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Lower Body Measurements --}}
                            @if (count($parsedMeas['lower']) > 0)
                                <div class="{{ count($parsedMeas['upper']) > 0 ? 'pt-2 border-t border-slate-100' : '' }}">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-teal-700 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5"/></svg>
                                        Lower Body Measurements
                                    </h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach ($parsedMeas['lower'] as $key => $val)
                                            <div class="bg-teal-50/50 border border-teal-100 rounded-xl p-2.5">
                                                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">{{ (string)$key }}</div>
                                                <div class="text-sm font-extrabold text-slate-800 mt-0.5">
                                                    {{ (string)$val }} <span class="text-[10px] font-medium text-slate-400">in</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Additional Measurements --}}
                            @if (count($parsedMeas['other']) > 0)
                                <div class="pt-2 border-t border-slate-100">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                                        Additional Measurements
                                    </h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        @foreach ($parsedMeas['other'] as $key => $val)
                                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-2.5">
                                                <div class="text-[10px] font-semibold text-slate-500 uppercase tracking-wide">{{ (string)$key }}</div>
                                                <div class="text-sm font-extrabold text-slate-800 mt-0.5">
                                                    {{ (string)$val }} <span class="text-[10px] font-medium text-slate-400">in</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Raw Text Measurements --}}
                            @if (!empty($parsedMeas['text']))
                                <div class="pt-2 border-t border-slate-100">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Measurement Notes</h4>
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-700 whitespace-pre-line font-medium">
                                        {{ $parsedMeas['text'] }}
                                    </div>
                                </div>
                            @endif

                            @if (empty($parsedMeas['upper']) && empty($parsedMeas['lower']) && empty($parsedMeas['other']) && empty($parsedMeas['text']))
                                <div class="text-center py-6 text-slate-400 text-xs italic">
                                    No specific measurements recorded for this order.
                                </div>
                            @endif
                        </div>

                        {{-- Modal Footer --}}
                        <div class="px-5 py-3 bg-slate-50 border-t border-slate-200 flex justify-end flex-shrink-0">
                            <button type="button" @click="activeModalOrder = null" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs rounded-xl transition">
                                Close
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Navigation Bar --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between gap-4 mt-6">
            <button type="button" @click="$refs.backForm.submit()" class="inline-flex items-center gap-1.5 px-4 py-2.5 border border-slate-300 text-slate-700 font-semibold text-xs rounded-xl hover:bg-slate-50 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Back to Cutting Assignment
            </button>

            <button type="submit" class="group inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/25 transition transform hover:scale-[1.01] active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                <span>Save Worker Assignments</span>
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
    function stitchingAssignmentWizard() {
        const defaults = {};
        @foreach ($orders as $order)
            defaults[{{ $order->id }}] = '{{ $order->stitching_employee_id }}';
        @endforeach

        return {
            bulkStitchingId: '',
            activeModalOrder: null,
            stitchingAssignments: defaults,
            init() {
                this.$watch('bulkStitchingId', () => this.applyBulkStitching());
            },
            applyBulkStitching() {
                if (!this.bulkStitchingId) return;
                Object.keys(this.stitchingAssignments).forEach(id => {
                    this.stitchingAssignments[id] = String(this.bulkStitchingId);
                });
            }
        }
    }
</script>
@endpush
@endsection
