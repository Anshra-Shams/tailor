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
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Measurements</h3>
            @if (!empty($measurement->data))
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach ($measurement->data as $key => $val)
                        <div class="bg-slate-50 rounded-lg px-3 py-2">
                            <div class="text-[11px] uppercase tracking-wide text-slate-400 font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}</div>
                            <div class="text-sm font-semibold text-slate-700 mt-0.5">{{ $val }} <span class="text-slate-400 font-normal text-xs">in</span></div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-400">No measurements recorded.</p>
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
                <div class="text-xs uppercase tracking-wide text-slate-400 font-medium">Service</div>
                <div class="text-sm font-medium text-slate-700 mt-0.5">{{ $measurement->service->name ?? '—' }}</div>
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
            <form method="POST" action="{{ route('measurements.destroy', $measurement) }}" onsubmit="return confirm('Delete this measurement permanently?')">
                @csrf @method('DELETE')
                <button class="w-full px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition">
                    Delete Measurement
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
