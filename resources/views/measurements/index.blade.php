@extends('layouts.app')

@section('title', 'Measurements')

@section('content')

<div class="space-y-6" x-data="measurementsIndexManager()">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Measurements</h2>
            <p class="text-sm text-slate-500">Manage all customer measurements</p>
        </div>
        <a href="{{ route('measurements.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Measurement
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <form method="GET" action="{{ route('measurements.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer name, member name, or service..."
                    class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
            </div>
        </form>
    </div>

    <div>

    @if ($measurements->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            <p class="text-slate-500 font-medium">No measurements yet</p>
            <a href="{{ route('measurements.create') }}" class="mt-3 inline-block text-sm text-indigo-600 font-semibold hover:underline">Create your first measurement →</a>
        </div>
    @else

        {{-- MOBILE: Card Layout --}}
        <div class="space-y-4 md:hidden">
            @foreach ($measurements as $measurement)
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

                    $viewData = [
                        'id'             => $measurement->id,
                        'customer_name'  => $measurement->customer->name ?? '—',
                        'customer_phone' => $measurement->customer->phone ?? '—',
                        'member_name'    => $measurement->member ? ($measurement->member->name . ($measurement->member->relation ? ' (' . ucfirst($measurement->member->relation) . ')' : '')) : 'Self',
                        'service_name'   => $measurement->service ? $measurement->service->name : 'General Profile',
                        'date_created'   => $measurement->created_at?->format('M d, Y') ?? '—',
                        'notes'          => $measurement->notes,
                        'upper_specs'    => $upperSpecs,
                        'lower_specs'    => $lowerSpecs,
                        'edit_url'       => route('measurements.edit', $measurement),
                        'destroy_url'    => route('measurements.destroy', $measurement),
                    ];
                @endphp
                <div class="block bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-mono text-xs text-slate-400">#{{ $measurement->id }}</span>
                        <span class="text-xs text-slate-400">{{ $measurement->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            <span class="text-sm font-semibold text-slate-800">{{ $measurement->customer->name ?? '—' }}</span>
                            @if ($measurement->member)
                                <span class="text-xs text-slate-400">/</span>
                                <span class="text-sm text-slate-500">{{ $measurement->member->name }}</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" /></svg>
                            @if($measurement->service)
                                <span class="text-sm text-slate-600">{{ $measurement->service->name }}</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">General Measurements</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100">
                        <button type="button" @click.prevent="openViewModal({{ json_encode($viewData) }})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            View
                        </button>
                        <a href="{{ route('measurements.edit', $measurement) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('measurements.destroy', $measurement) }}" class="js-delete-form flex-1" data-title="Delete Measurement?">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- DESKTOP: Table Layout --}}
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">#</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Customer</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Member</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Service</th>
                            <th class="text-left px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Date</th>
                            <th class="text-right px-5 py-3 font-semibold text-slate-600 whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($measurements as $measurement)
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

                                $viewData = [
                                    'id'             => $measurement->id,
                                    'customer_name'  => $measurement->customer->name ?? '—',
                                    'customer_phone' => $measurement->customer->phone ?? '—',
                                    'member_name'    => $measurement->member ? ($measurement->member->name . ($measurement->member->relation ? ' (' . ucfirst($measurement->member->relation) . ')' : '')) : 'Self',
                                    'service_name'   => $measurement->service ? $measurement->service->name : 'General Profile',
                                    'date_created'   => $measurement->created_at?->format('M d, Y') ?? '—',
                                    'notes'          => $measurement->notes,
                                    'upper_specs'    => $upperSpecs,
                                    'lower_specs'    => $lowerSpecs,
                                    'edit_url'       => route('measurements.edit', $measurement),
                                    'destroy_url'    => route('measurements.destroy', $measurement),
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-3 font-mono text-slate-500 text-xs whitespace-nowrap">#{{ $measurement->id }}</td>
                                <td class="px-5 py-3 font-medium text-slate-700 whitespace-nowrap">{{ $measurement->customer->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 whitespace-nowrap">{{ $measurement->member->name ?? '—' }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">
                                    @if($measurement->service)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700">
                                            {{ $measurement->service->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            General Profile
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-slate-500 text-xs whitespace-nowrap">{{ $measurement->created_at->format('M d, Y') }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" @click.prevent="openViewModal({{ json_encode($viewData) }})" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition cursor-pointer" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                        <a href="{{ route('measurements.edit', $measurement) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('measurements.destroy', $measurement) }}" class="js-delete-form" data-title="Delete Measurement?">
                                            @csrf @method('DELETE')
                                            <button class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 px-6 py-4">
            {{ $measurements->links() }}
        </div>

    @endif
    </div>

    {{-- View Measurement Modal --}}
    <div x-show="viewModalOpen" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
        @keydown.escape.window="closeViewModal()">
        
        <div class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden border border-slate-200 transform transition-all"
            @click.away="closeViewModal()">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                        #<span x-text="viewData?.id"></span>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800" x-text="viewData?.customer_name"></h3>
                        <p class="text-xs text-slate-500" x-text="(viewData?.service_name || 'General Profile') + ' • ' + (viewData?.date_created || '')"></p>
                    </div>
                </div>
                <button type="button" @click="closeViewModal()" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5 overflow-y-auto max-h-[calc(90vh-140px)]">
                
                {{-- Customer & Profile Summary Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    {{-- Customer Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3 flex items-start gap-2.5">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Customer</span>
                            <span class="font-bold text-slate-800 text-xs block truncate" x-text="viewData?.customer_name"></span>
                            <span class="text-slate-500 text-[11px] block truncate" x-text="viewData?.customer_phone"></span>
                        </div>
                    </div>

                    {{-- Member Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3 flex items-start gap-2.5">
                        <div class="p-2 rounded-lg bg-emerald-50 text-emerald-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Measurement For</span>
                            <span class="font-bold text-slate-800 text-xs block truncate" x-text="viewData?.member_name"></span>
                        </div>
                    </div>

                    {{-- Service/Type Card --}}
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-xl p-3 flex items-start gap-2.5">
                        <div class="p-2 rounded-lg bg-amber-50 text-amber-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Type / Service</span>
                            <span class="font-bold text-slate-800 text-xs block truncate" x-text="viewData?.service_name"></span>
                        </div>
                    </div>
                </div>

                {{-- Upper Body Measurements Section --}}
                <template x-if="Object.keys(viewData?.upper_specs || {}).length > 0">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                            <span class="text-base">👕</span>
                            <h4 class="text-xs font-bold text-indigo-700 uppercase tracking-wider">Upper Body Measurements</h4>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <template x-for="(vals, label) in viewData.upper_specs" :key="'mup_'+label">
                                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl px-3 py-2">
                                    <div class="text-[11px] uppercase tracking-wide text-indigo-900 font-bold" x-text="label"></div>
                                    <div class="flex flex-wrap items-center gap-1 mt-1">
                                        <template x-for="(v, idx) in vals" :key="idx">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-white text-indigo-700 border border-indigo-200 shadow-2xs">
                                                <span x-text="v"></span>
                                                <span class="text-slate-400 font-normal text-[10px] ml-0.5">in</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Lower Body Measurements Section --}}
                <template x-if="Object.keys(viewData?.lower_specs || {}).length > 0">
                    <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                            <span class="text-base">👖</span>
                            <h4 class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Lower Body Measurements</h4>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                            <template x-for="(vals, label) in viewData.lower_specs" :key="'mlw_'+label">
                                <div class="bg-emerald-50/60 border border-emerald-100 rounded-xl px-3 py-2">
                                    <div class="text-[11px] uppercase tracking-wide text-emerald-900 font-bold" x-text="label"></div>
                                    <div class="flex flex-wrap items-center gap-1 mt-1">
                                        <template x-for="(v, idx) in vals" :key="idx">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-white text-emerald-700 border border-emerald-200 shadow-2xs">
                                                <span x-text="v"></span>
                                                <span class="text-slate-400 font-normal text-[10px] ml-0.5">in</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Special Instructions / Notes --}}
                <template x-if="viewData?.notes">
                    <div class="bg-amber-50/60 border border-amber-200/80 rounded-xl p-3.5">
                        <h5 class="text-xs font-bold text-amber-900 mb-1 flex items-center gap-1.5">
                            <span>📝</span> Special Instructions:
                        </h5>
                        <p class="text-xs text-amber-800 leading-relaxed" x-text="viewData?.notes"></p>
                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" @click="closeViewModal()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-100 transition cursor-pointer">
                    Close
                </button>
                <div class="flex items-center gap-2">
                    <a :href="viewData?.edit_url" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                        Edit Measurement
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
function measurementsIndexManager() {
    return {
        viewModalOpen: false,
        viewData: null,
        openViewModal(data) {
            this.viewData = data;
            this.viewModalOpen = true;
        },
        closeViewModal() {
            this.viewModalOpen = false;
            this.viewData = null;
        }
    };
}
</script>

@endsection
