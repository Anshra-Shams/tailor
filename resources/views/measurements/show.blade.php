@extends('layouts.app')

@section('title', 'Measurement #' . $measurement->id)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('measurements.index') }}" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
    </a>
    <div>
        <h2 class="text-xl font-bold text-slate-800">Measurement #{{ $measurement->id }}</h2>
        <p class="text-sm text-slate-500">{{ $measurement->service->name ?? '—' }} for {{ $measurement->customer->name ?? '—' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Details --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Measurements --}}
        @php
            $data = $measurement->data ?? [];
            $customFieldDefs = collect($data['__custom_fields'] ?? [])->keyBy('key');

            $upperSpecs = [];
            $lowerSpecs = [];
            $lowerKeywords = ['shalwar', 'trouser', 'paincha', 'bottom', 'pant', 'lower', 'asan', 'fly', 'inseam', 'thigh', 'thai', 'knee', 'ankle', 'pajama', 'belt', 'elastic', 'hip'];

            foreach ($data as $key => $val) {
                if ($key === '__custom_fields' || $key === '__style') {
                    continue;
                }
                
                if ($key === '__custom' && is_array($val)) {
                    foreach ($val as $cf) {
                        if (!empty($cf['label']) && !empty($cf['value'])) {
                            $lbl = trim($cf['label']);
                            $lblLower = strtolower($lbl);
                            $isLower = false;
                            foreach ($lowerKeywords as $kw) {
                                if (str_contains($lblLower, $kw)) {
                                    $isLower = true;
                                    break;
                                }
                            }
                            $vals = is_array($cf['value']) ? $cf['value'] : [$cf['value']];
                            if ($isLower) {
                                $lowerSpecs[$lbl] = $vals;
                            } else {
                                $upperSpecs[$lbl] = $vals;
                            }
                        }
                    }
                    continue;
                }

                $fieldDef = $customFieldDefs->get($key);
                if ($fieldDef) {
                    $displayLabel = $fieldDef['label'] . (!empty($fieldDef['urdu']) ? ' (' . $fieldDef['urdu'] . ')' : '');
                    $fType = strtolower($fieldDef['type'] ?? '');
                } else {
                    $cleanLabel = preg_replace('/^custom_[ul]_/i', '', $key);
                    $cleanLabel = preg_replace('/^custom_/i', '', $cleanLabel);
                    $cleanLabel = preg_replace('/_\d+$/', '', $cleanLabel);
                    $cleanLabel = preg_replace('/\b(upper|lower)\b/i', '', $cleanLabel);
                    $displayLabel = trim(ucwords(preg_replace('/\s+/', ' ', str_replace('_', ' ', $cleanLabel))));
                    $displayLabel = preg_replace('/\s+\d+$/', '', $displayLabel);
                    if (empty($displayLabel)) {
                        $displayLabel = ucwords(str_replace('_', ' ', $key));
                    }
                    $fType = '';
                }

                $keyLower = strtolower($key);
                $isLower = false;

                if ($fType === 'lower') {
                    $isLower = true;
                } elseif ($fType === 'upper') {
                    $isLower = false;
                } elseif (str_starts_with($keyLower, 'custom_l_')) {
                    $isLower = true;
                } elseif (str_starts_with($keyLower, 'custom_u_')) {
                    $isLower = false;
                } else {
                    foreach ($lowerKeywords as $kw) {
                        if (str_contains($keyLower, $kw)) {
                            $isLower = true;
                            break;
                        }
                    }
                }

                $valArray = is_array($val) ? $val : ([trim((string)$val) !== '' ? (string)$val : null]);
                $valArray = array_values(array_filter($valArray, fn($v) => $v !== null && $v !== ''));

                if (!empty($valArray)) {
                    if ($isLower) {
                        $lowerSpecs[$displayLabel] = $valArray;
                    } else {
                        $upperSpecs[$displayLabel] = $valArray;
                    }
                }
            }
        @endphp

        <div class="space-y-5">
            @if (!empty($upperSpecs))
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <h3 class="font-bold text-indigo-700 text-sm mb-3 flex items-center gap-2">
                        <span>👕</span> Upper Body Measurements
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($upperSpecs as $label => $vals)
                            <div class="bg-indigo-50/60 rounded-xl border border-indigo-100 px-3 py-2.5">
                                <div class="text-[11px] uppercase tracking-wide text-indigo-900 font-bold">{{ $label }}</div>
                                <div class="flex flex-wrap items-center gap-1 mt-1">
                                    @foreach($vals as $v)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-white text-indigo-700 border border-indigo-200 shadow-2xs">{{ $v }} <span class="text-slate-400 font-normal text-[10px] ml-0.5">in</span></span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($lowerSpecs))
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                    <h3 class="font-bold text-emerald-700 text-sm mb-3 flex items-center gap-2">
                        <span>👖</span> Lower Body Measurements
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($lowerSpecs as $label => $vals)
                            <div class="bg-emerald-50/60 rounded-xl border border-emerald-100 px-3 py-2.5">
                                <div class="text-[11px] uppercase tracking-wide text-emerald-900 font-bold">{{ $label }}</div>
                                <div class="flex flex-wrap items-center gap-1 mt-1">
                                    @foreach($vals as $v)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-white text-emerald-700 border border-emerald-200 shadow-2xs">{{ $v }} <span class="text-slate-400 font-normal text-[10px] ml-0.5">in</span></span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (empty($upperSpecs) && empty($lowerSpecs))
                <div class="bg-white rounded-2xl border border-slate-200 p-5 text-center text-slate-400 text-sm">
                    No measurements recorded.
                </div>
            @endif
        </div>

        {{-- Notes --}}
        @if ($measurement->notes)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-semibold text-slate-800 mb-2">Special Instructions</h3>
                <p class="text-sm text-slate-600">{{ $measurement->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Sidebar Info --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Customer</div>
                <a href="{{ route('customers.show', $measurement->customer_id) }}" class="text-sm font-semibold text-indigo-600 hover:underline mt-0.5 block">
                    {{ $measurement->customer->name ?? '—' }}
                </a>
            </div>
            @if ($measurement->member)
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Member</div>
                    <div class="text-sm font-medium text-slate-700 mt-0.5">{{ $measurement->member->name }}</div>
                </div>
            @endif
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Measurement Type</div>
                <div class="text-sm font-medium text-slate-700 mt-0.5">
                    @if($measurement->service)
                        {{ $measurement->service->name }}
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">General Measurements</span>
                    @endif
                </div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Date Created</div>
                <div class="text-sm text-slate-700 mt-0.5">{{ $measurement->created_at?->format('M d, Y') ?? '—' }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-2">
            <a href="{{ route('measurements.edit', $measurement) }}" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-xl hover:bg-indigo-100 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                Edit Measurement
            </a>
            <form method="POST" action="{{ route('measurements.destroy', $measurement) }}" class="js-delete-form" data-title="Delete Measurement?">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition">
                    Delete Measurement
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
