@extends('layouts.app')

@section('title', $service->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Services
        </a>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $service->name }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        @if($service->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-100 transition-all text-sm">Edit</a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-500 mb-1">Price</h3>
            <p class="text-3xl font-bold text-indigo-600">Rs. {{ number_format($service->price, 0) }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-500 mb-1">Est. Days</h3>
            <p class="text-3xl font-bold text-slate-800">{{ $service->estimated_days ?: '—' }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h3 class="text-sm font-semibold text-slate-500 mb-1">Total Orders</h3>
            <p class="text-3xl font-bold text-slate-800">{{ $service->orders->count() }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-3">Description</h3>
        <p class="text-slate-600">{{ $service->description ?: 'No description provided.' }}</p>
    </div>
</div>
@endsection
