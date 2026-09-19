@extends('layouts.app')

@section('title', 'Services')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Services</h2>
            <p class="text-slate-500 mt-1">Manage your tailoring services</p>
        </div>
        <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Service
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <form method="GET" action="{{ route('services.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search services..."
                    class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
            </div>
        </form>
    </div>

    @if($services->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($services as $service)
                <div class="bg-white rounded-2xl border border-slate-200 p-6 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-2xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center transition-colors">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                            </svg>
                        </div>
                        @if($service->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">Inactive</span>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">{{ $service->name }}</h3>
                    <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $service->description ?: 'No description' }}</p>
                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                        <a href="{{ route('services.edit', $service) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-50 text-indigo-600 text-sm font-medium rounded-lg hover:bg-indigo-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" /></svg>
                            Edit
                        </a>
                        <button type="button" title="View measurement fields" class="js-view-fields px-3 py-2 bg-purple-50 text-purple-600 text-sm font-medium rounded-lg hover:bg-purple-100 transition-all" data-id="{{ $service->id }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </button>
                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="js-delete-form" data-name="{{ $service->name }}" data-title="Delete Service?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $services->links() }}</div>
    @else
        <div class="bg-white rounded-2xl border border-slate-200 px-6 py-16 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" /></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">No services found</h3>
            <p class="text-slate-500 mt-1">Create your first service to get started.</p>
            <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Service
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    .swal2-popup {
        padding-top: 1.4em !important;
        padding-right: 2em !important;
        padding-left: 2em !important;
    }
    .swal2-popup .swal2-title {
        padding-right: 40px;
        margin-top: 6px;
    }
    .swal2-popup .swal2-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #94a3b8;
        transition: all .15s ease;
    }
    .swal2-popup .swal2-close:hover {
        color: #ef4444;
        background: #fee2e2;
        transform: none;
    }
</style>
<script>
window.__serviceFields = @json($services->mapWithKeys(fn ($s) => [
    $s->id => ['name' => $s->name, 'fields' => $s->measurement_fields ?? []],
]));

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

function viewServiceFields(name, fields) {
    const list = Array.isArray(fields) ? fields : [];
    let html;

    if (!list.length) {
        html = '<p class="text-sm text-slate-500 py-4">No measurement fields defined for this service.</p>';
    } else {
        const rows = list.map((f, i) => `
            <div class="flex items-center justify-between gap-3 px-3 py-2 rounded-lg border ${i % 2 ? 'bg-slate-50' : 'bg-white'} border-slate-100">
                <span class="flex items-center gap-2 min-w-0">
                    <span class="w-5 h-5 rounded-md bg-indigo-100 text-indigo-600 text-[11px] font-bold flex items-center justify-center flex-shrink-0">${i + 1}</span>
                    <span class="text-sm font-medium text-slate-700 truncate">${escapeHtml(f.label || f.key)}</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded ${f.type === 'lower' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200'}">${f.type === 'lower' ? '👖 Lower' : '👕 Upper'}</span>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full whitespace-nowrap ${f.required ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-slate-100 text-slate-400 border border-slate-200'}">${f.required ? 'Required' : 'Optional'}</span>
            </div>`).join('');
        html = `<p class="text-xs text-slate-400 mb-3">${list.length} field${list.length === 1 ? '' : 's'}</p><div class="space-y-1.5 text-left max-h-72 overflow-y-auto pr-1">${rows}</div>`;
    }

    Swal.fire({
        title: `<span class="text-lg font-bold text-slate-800">${escapeHtml(name)}</span>`,
        html: html,
        showConfirmButton: false,
        showCloseButton: true,
        closeButtonHtml: `
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;display:block">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>`,
        width: 420,
        customClass: { popup: 'rounded-2xl' },
    });
}

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.js-view-fields');
    if (!btn) return;
    const data = (window.__serviceFields || {})[btn.dataset.id];
    if (!data) return;
    viewServiceFields(data.name, data.fields);
});
</script>
@endpush
