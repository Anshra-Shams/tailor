@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="roleForm()">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Roles
                </a>
                <span>/</span>
                <span class="text-slate-400">Edit</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Edit Role: {{ $role->name }}</h2>
                    <p class="text-xs text-slate-500">Update role name and customize assigned permissions</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}" 
               class="px-5 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 rounded-xl transition shadow-xs">
                Cancel
            </a>
            <button type="submit" form="role-edit-form" 
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-indigo-500/25 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Update Role
            </button>
        </div>
    </div>

    <!-- Error Validation Banner -->
    @if (isset($errors) && $errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-red-800">Please correct the errors below:</h4>
                <ul class="list-disc list-inside text-xs text-red-600 mt-1 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="role-edit-form" method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Role Name Input Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                Role Name <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $role->name) }}" 
                   required 
                   {{ $role->name === 'Super Admin' ? 'readonly' : '' }}
                   class="w-full md:w-2/3 px-4 py-2.5 border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition {{ $role->name === 'Super Admin' ? 'bg-slate-100 cursor-not-allowed' : '' }}">
            @if($role->name === 'Super Admin')
                <p class="text-xs text-amber-600 mt-1.5 font-medium">The 'Super Admin' role name is protected by the system.</p>
            @endif
        </div>

        <!-- Permissions Section Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
            <div>
                <h3 class="text-base font-bold text-slate-800">Assign Permissions</h3>
                <p class="text-xs text-slate-500">Enable features and data access for this role</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="toggleSelectAll(true)" class="px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                    Select All
                </button>
                <button type="button" @click="toggleSelectAll(false)" class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                    Deselect All
                </button>
            </div>
        </div>

        <!-- Grouped Permissions Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($groupedPermissions as $groupName => $permissions)
                @php
                    $groupSlug = \Illuminate\Support\Str::slug($groupName);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <h4 class="font-bold text-slate-800 text-sm">{{ $groupName }}</h4>
                        </div>
                        <button type="button" 
                                @click="toggleGroup('{{ $groupSlug }}')" 
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition">
                            Toggle Group
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                        @foreach($permissions as $permission)
                            <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate-50 cursor-pointer transition">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $permission->name }}"
                                       data-group="{{ $groupSlug }}"
                                       {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                       class="perm-checkbox w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                <span class="text-xs font-medium text-slate-700 capitalize">
                                    {{ str_replace('-', ' ', $permission->name) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

<script>
    function roleForm() {
        return {
            toggleSelectAll(checked) {
                document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = checked);
            },
            toggleGroup(group) {
                const cbs = document.querySelectorAll(`.perm-checkbox[data-group="${group}"]`);
                const anyUnchecked = Array.from(cbs).some(cb => !cb.checked);
                cbs.forEach(cb => cb.checked = anyUnchecked);
            }
        };
    }
</script>
@endsection
