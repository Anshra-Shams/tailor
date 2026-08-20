@extends('layouts.app')

@section('title', 'Edit Service')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Services
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Edit Service</h2>
        <p class="text-slate-500 mt-1">Update service details and pricing.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('services.update', $service) }}" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Service Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" required
                    class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    placeholder="e.g. Shalwar Kameez">
            </div>
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                    placeholder="Describe the service...">{{ old('description', $service->description) }}</textarea>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="price" class="block text-sm font-semibold text-slate-700 mb-1.5">Price (Rs.) <span class="text-red-500">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price', $service->price) }}" required min="0" step="0.01"
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label for="estimated_days" class="block text-sm font-semibold text-slate-700 mb-1.5">Estimated Days</label>
                    <input type="number" id="estimated_days" name="estimated_days" value="{{ old('estimated_days', $service->estimated_days) }}" min="1"
                        class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">Active service</label>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200">
                    Update Service
                </button>
                <a href="{{ route('services.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-200">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
