@extends('layouts.app')

@section('title', 'Designations')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Designations</h2>
            <p class="text-sm text-slate-500 mt-1">Manage staff designations and job titles</p>
        </div>
        <button onclick="document.getElementById('createDesignationModal').classList.remove('hidden')" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add Designation
        </button>
    </div>

    <!-- Search Bar (Above Table) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('designations.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Search designation by title..." 
                       class="block w-full pl-11 pr-10 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-sm">
                @if(!empty($search))
                    <a href="{{ route('designations.index') }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-xs font-semibold uppercase text-slate-500 tracking-wider">
                        <th class="px-6 py-4">#</th>
                        <th class="px-6 py-4">Designation Title</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Employees Count</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($designations as $designation)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-medium text-slate-400">{{ $loop->iteration + ($designations->currentPage() - 1) * $designations->perPage() }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $designation->name }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $designation->description ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 border border-purple-100">
                                    {{ $designation->employees_count }} Employees
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                <!-- Edit Button -->
                                <button type="button" 
                                        onclick="openEditDesignationModal({{ json_encode($designation) }})"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition" 
                                        title="Edit Designation">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <form action="{{ route('designations.destroy', $designation) }}" method="POST" class="inline js-delete-form" data-name="{{ $designation->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                @if(!empty($search))
                                    No designations match "{{ $search }}". <a href="{{ route('designations.index') }}" class="text-indigo-600 underline">Clear search</a>
                                @else
                                    No designations found. Click "Add Designation" to create one.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($designations->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $designations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="createDesignationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Add New Designation</h3>
        <form action="{{ route('designations.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Designation Title *</label>
                <input type="text" name="name" required class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3.5 py-2.5" placeholder="e.g. Master Tailor, Cutter, Assistant">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3.5 py-2.5"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createDesignationModal').classList.add('hidden')" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editDesignationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl relative border border-slate-100">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Edit Designation</h3>
        <form id="editDesignationForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Designation Title *</label>
                <input type="text" id="edit_designation_name" name="name" required class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3.5 py-2.5">
            </div>
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Description</label>
                <textarea id="edit_designation_description" name="description" rows="3" class="w-full border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 px-3.5 py-2.5"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editDesignationModal').classList.add('hidden')" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-500/20">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditDesignationModal(desig) {
        document.getElementById('editDesignationForm').action = '/designations/' + desig.id;
        document.getElementById('edit_designation_name').value = desig.name || '';
        document.getElementById('edit_designation_description').value = desig.description || '';
        document.getElementById('editDesignationModal').classList.remove('hidden');
    }
</script>
@endsection
