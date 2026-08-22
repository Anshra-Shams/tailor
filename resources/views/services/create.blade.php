@extends('layouts.app')

@section('title', 'Add Service')

@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="serviceForm()">
    <div>
        <a href="{{ route('services.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Services
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Add New Service</h2>
        <p class="text-slate-500 mt-1">Create a new tailoring service with measurements.</p>
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

    <form method="POST" action="{{ route('services.store') }}">
        @csrf

        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Service Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    placeholder="e.g. Shalwar Kameez">
            </div>
            <div>
                <label for="description" class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all resize-none"
                    placeholder="Describe the service...">{{ old('description') }}</textarea>
            </div>
            <div>
                <label for="price" class="block text-sm font-semibold text-slate-700 mb-1.5">Base Price (Rs) <span class="text-red-500">*</span></label>
                <input type="number" id="price" name="price" value="{{ old('price', 0) }}" min="0" step="50" required
                    class="block w-full px-4 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                    placeholder="e.g. 1500">
            </div>
            <div class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                <label for="is_active" class="text-sm font-medium text-slate-700">Active service</label>
            </div>
        </div>

        {{-- Measurement Fields --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 mt-5 space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Measurement Fields</h3>
                    <p class="text-sm text-slate-500 mt-0.5">Define measurements needed for this service</p>
                </div>
                <button type="button" @click="addField()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add Field
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                <template x-for="(field, index) in fields" :key="index">
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200" x-transition>
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Label</label>
                    <div class="flex items-center gap-2">
                        <input type="text" :name="'measurement_fields[' + index + '][label]'" x-model="field.label" required
                            class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                            placeholder="e.g. Chest">
                        <label class="inline-flex items-center gap-1 cursor-pointer shrink-0">
                            <input type="checkbox" :name="'measurement_fields[' + index + '][required]'" x-model="field.required" :value="1" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs font-medium text-slate-600">Req</span>
                        </label>
                        <button type="button" @click="removeField(index)" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </template>
            </div>

            <div x-show="fields.length === 0" class="bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 p-6 text-center">
                <p class="text-sm text-slate-500">No measurement fields yet. Click <strong>"Add Field"</strong> to define measurements.</p>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-5">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all duration-200">
                Save Service
            </button>
            <a href="{{ route('services.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-200">
                Cancel
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
function serviceForm() {
    return {
        fields: [],
        addField() {
            this.fields.push({ label: '', required: true });
        },
        removeField(index) {
            this.fields.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection
