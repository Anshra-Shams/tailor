@extends('layouts.app')

@section('title', 'Users')

@section('content')
@php
    $editUser = request('open_edit') ? ($users->firstWhere('id', request('open_edit')) ?? \App\Models\User::with('roles')->find(request('open_edit'))) : null;
@endphp

<div class="space-y-6" x-data="userManagement()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Users Management</h2>
            <p class="text-sm text-slate-500 mt-1">Manage system accounts, user credentials, and assigned roles</p>
        </div>
        <button type="button"
                @click="openCreate()" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow-lg shadow-indigo-500/20 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New User
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Users</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalUsers }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Available Roles</p>
                <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ $totalRoles }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Verified Accounts</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $verifiedUsers }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Search user by name or email..." 
                       class="block w-full pl-11 pr-10 py-2.5 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                @if(!empty($search))
                    <a href="{{ route('users.index', array_filter(['role' => $roleFilter])) }}" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600" title="Clear search">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </a>
                @endif
            </div>

            <div class="sm:w-60">
                <select name="role" 
                        onchange="this.form.submit()" 
                        class="block w-full py-2.5 px-3 border border-slate-200 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ ($roleFilter ?? '') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
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
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Assigned Roles</th>
                        <th class="px-6 py-4">Created Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-6 py-4 font-medium text-slate-400">
                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 flex items-center gap-2">
                                            {{ $user->name }}
                                            @if(auth()->id() === $user->id)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">You</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($user->roles as $userRole)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $userRole->name === 'Super Admin' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-100' }}">
                                            {{ $userRole->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400 italic">No roles assigned</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                <div>{{ $user->created_at ? $user->created_at->format('M d, Y') : '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                <button type="button" 
                                        @click="openEdit({{ json_encode($user) }}, {{ json_encode($user->roles->pluck('name')) }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition"
                                        title="Edit User">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Edit
                                </button>

                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user) }}" 
                                          method="POST" 
                                          class="inline-block js-delete-form"
                                          data-title="Delete User?"
                                          data-name="{{ $user->name }} ({{ $user->email }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition"
                                                title="Delete User">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-slate-700">No users found</p>
                                    <p class="text-xs text-slate-400 mt-1">Try adjusting your search criteria or create a new user account.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- User Create / Edit Popup Modal (Matches Image Design) -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity" @click="closeModal()"></div>

        <!-- Centering wrapper -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-200"
                 @click.stop>
                
                <!-- Modal Header (Purple Bar Matching Image) -->
                <div class="px-6 py-4 flex items-center justify-between rounded-t-3xl"
                     style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #ffffff;">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <h3 class="text-lg font-bold tracking-tight text-white" x-text="isEdit ? 'Edit User' : 'Add New User'">Add New User</h3>
                    </div>
                    <button type="button" @click="closeModal()" class="text-white/80 hover:text-white transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Form -->
                <form :action="formAction" method="POST" class="p-6 sm:p-7 space-y-5">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <!-- Validation Errors Inside Modal -->
                    @if (isset($errors) && $errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <ul class="list-disc list-inside text-xs text-red-600 space-y-0.5 font-medium">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Row 1: Full Name & Email Address -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-800 mb-1.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                <span>Full Name</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   x-model="formData.name" 
                                   required 
                                   placeholder="Enter full name" 
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>

                        <div>
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-800 mb-1.5">
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                <span>Email Address</span>
                            </label>
                            <input type="email" 
                                   name="email" 
                                   x-model="formData.email" 
                                   required 
                                   placeholder="Enter email address" 
                                   class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-800 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                        </div>
                    </div>

                    <!-- Row 2: Password with Attached Eye Toggle Button -->
                    <div>
                        <label class="flex items-center gap-2 text-sm font-bold text-slate-800 mb-1.5">
                            <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Password</span>
                        </label>
                        <div class="flex items-stretch w-full">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   x-model="formData.password"
                                   :required="!isEdit"
                                   minlength="6"
                                   :placeholder="isEdit ? 'Enter password (leave blank to keep existing)' : 'Enter password'" 
                                   class="flex-1 min-w-0 px-4 py-2.5 bg-white border border-slate-300 rounded-l-xl rounded-r-none text-slate-800 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 focus:z-10 transition">
                            
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="px-3.5 flex items-center justify-center rounded-r-xl border border-l-0 border-purple-400 bg-white text-purple-600 hover:bg-purple-50 transition flex-shrink-0 cursor-pointer"
                                    title="Toggle Password Visibility">
                                <svg x-show="!showPassword" class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Row 3: Assign Roles -->
                    <div class="space-y-2.5 pt-1">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-slate-800">
                                <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                </svg>
                                <span>Assign Roles</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                                <input type="checkbox" 
                                       x-model="selectAllRoles" 
                                       @change="toggleSelectAll()" 
                                       class="w-4 h-4 text-purple-600 border-slate-300 rounded focus:ring-purple-500">
                                <span>Select All</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 hover:border-purple-300 cursor-pointer transition select-none">
                                    <input type="checkbox" 
                                           name="roles[]" 
                                           value="{{ $role->name }}" 
                                           x-model="formData.roles"
                                           @change="checkSelectAll()"
                                           class="w-4 h-4 text-purple-600 border-slate-300 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium text-slate-800">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="closeModal()" 
                                style="background-color: #f1f5f9; color: #334155;"
                                class="inline-flex items-center gap-1.5 px-6 py-2.5 text-sm font-bold rounded-xl hover:bg-slate-200 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Cancel</span>
                        </button>
                        <button type="submit" 
                                style="background: linear-gradient(135deg, #7c3aed, #6366f1) !important; color: #ffffff !important;"
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-sm font-bold rounded-xl shadow-md transition hover:opacity-95 cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span x-text="isEdit ? 'Update User' : 'Create User'" class="text-white font-bold">Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        const allRoleNames = @json($roles->pluck('name'));
        const initialOpenCreate = {{ (request()->has('open_create') || (isset($errors) && $errors->any() && !old('_method'))) ? 'true' : 'false' }};
        const initialEditUser = @json($editUser);
        const initialOpenEdit = {{ (request()->has('open_edit') || (isset($errors) && $errors->any() && old('_method') === 'PUT')) ? 'true' : 'false' }};

        let isEditState = false;
        let action = '{{ route('users.store') }}';
        let initialData = {
            id: null,
            name: '{{ old('name', '') }}',
            email: '{{ old('email', '') }}',
            password: '',
            roles: @json(old('roles', []))
        };

        if (initialOpenEdit && initialEditUser) {
            isEditState = true;
            action = '{{ url('users') }}/' + initialEditUser.id;
            initialData = {
                id: initialEditUser.id,
                name: '{{ old('name') }}' || initialEditUser.name,
                email: '{{ old('email') }}' || initialEditUser.email,
                password: '',
                roles: @json(old('roles')) || (initialEditUser.roles ? initialEditUser.roles.map(r => r.name) : [])
            };
        }

        return {
            modalOpen: initialOpenCreate || (initialOpenEdit && !!initialEditUser),
            isEdit: isEditState,
            showPassword: false,
            selectAllRoles: false,
            formAction: action,
            formData: initialData,

            init() {
                this.checkSelectAll();
            },

            openCreate() {
                this.isEdit = false;
                this.formAction = '{{ route('users.store') }}';
                this.formData = { id: null, name: '', email: '', password: '', roles: [] };
                this.showPassword = false;
                this.selectAllRoles = false;
                this.modalOpen = true;
            },

            openEdit(user, userRoles) {
                this.isEdit = true;
                this.formAction = '{{ url('users') }}/' + user.id;
                this.formData = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    password: '',
                    roles: userRoles || []
                };
                this.showPassword = false;
                this.modalOpen = true;
                this.checkSelectAll();
            },

            closeModal() {
                this.modalOpen = false;
            },

            toggleSelectAll() {
                if (this.selectAllRoles) {
                    this.formData.roles = [...allRoleNames];
                } else {
                    this.formData.roles = [];
                }
            },

            checkSelectAll() {
                if (!allRoleNames || allRoleNames.length === 0) {
                    this.selectAllRoles = false;
                    return;
                }
                this.selectAllRoles = allRoleNames.every(r => this.formData.roles && this.formData.roles.includes(r));
            }
        };
    }
</script>
@endsection
