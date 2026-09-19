@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="space-y-6" x-data="customerManager()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Customers</h2>
            <p class="text-slate-500 mt-1">Manage customer and members</p>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Add Customer
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4">
        <form method="GET" action="{{ route('customers.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by customer name, phone, or member name..."
                    class="block w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
            </div>
        </form>
    </div>

    @if($customers->count())

        {{-- MOBILE: Card Layout --}}
        <div class="space-y-4 md:hidden">
            @foreach($customers as $customer)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                            {{ substr($customer->name, 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-slate-800 truncate">{{ $customer->name }}</p>
                            <p class="text-xs text-slate-500">{{ $customer->gender ? ucfirst($customer->gender) : '' }}</p>
                        </div>
                        @if($customer->members_count > 0)
                            <button type="button" class="js-members-badge inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 flex-shrink-0 hover:bg-indigo-100 transition-colors cursor-pointer" data-customer="{{ $customer->id }}" title="View member details">
                                Member {{ $customer->members_count }}
                            </button>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200 flex-shrink-0">
                                No Members
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <span class="text-sm text-slate-600">{{ $customer->phone }}</span>
                    </div>

                    @if($customer->address)
                        <div class="flex items-start gap-2 mb-3">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <span class="text-sm text-slate-500 line-clamp-2">{{ $customer->address }}</span>
                        </div>
                    @endif

                    <div class="flex items-center gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="openViewModal({{ $customer->id }})" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-all cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            View
                        </button>
                        <a href="{{ route('customers.edit', $customer) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                            Edit
                        </a>
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="js-delete-form flex-1" data-name="{{ $customer->name }}" data-title="Delete Customer?">
                            @csrf
                            @method('DELETE')
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
        <div class="hidden md:block bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50">
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Customer</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Phone</th>
                            <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Address</th>
                            <th class="text-center px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Members</th>
                            <th class="text-right px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                            {{ substr($customer->name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800 truncate">{{ $customer->name }}</p>
                                            <p class="text-xs text-slate-500 truncate">{{ $customer->gender ? ucfirst($customer->gender) : '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">{{ $customer->phone }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 max-w-[200px] truncate whitespace-nowrap">{{ $customer->address ?: '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($customer->members->count())
                                        <button type="button" class="js-members-badge inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 hover:border-indigo-300 transition-all cursor-pointer whitespace-nowrap" data-customer="{{ $customer->id }}" title="View member details">
                                            <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                            Member {{ $customer->members->count() }}
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                                            No Members
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" @click="openViewModal({{ $customer->id }})" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all cursor-pointer" title="View Customer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                        <a href="{{ route('customers.edit', $customer) }}" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="js-delete-form" data-name="{{ $customer->name }}" data-title="Delete Customer?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
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
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $customers->links() }}
            </div>
        </div>

    @else
        <div class="text-center py-16 bg-white rounded-2xl border border-slate-200">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800">No customers found</h3>
            <p class="text-slate-500 mt-1">Get started by adding your first customer family.</p>
            <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Customer
            </a>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════
         VIEW CUSTOMER MODAL (Compact & Clean)
    ═══════════════════════════════════════════════════ --}}
    <div x-show="viewModalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="closeViewModal()">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeViewModal()"></div>

        {{-- Dialog Box --}}
        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200/80 w-full max-w-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-base shadow-sm flex-shrink-0"
                        x-text="activeCustomer?.name ? activeCustomer.name.charAt(0).toUpperCase() : 'C'">
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base font-bold text-slate-800 truncate" x-text="activeCustomer?.name"></h3>
                            <template x-if="activeCustomer?.gender">
                                <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full border capitalize"
                                    :class="{
                                        'bg-blue-50 text-blue-700 border-blue-200': (activeCustomer.gender || '').toLowerCase() === 'male',
                                        'bg-pink-50 text-pink-700 border-pink-200': (activeCustomer.gender || '').toLowerCase() === 'female',
                                        'bg-slate-100 text-slate-600 border-slate-200': (activeCustomer.gender || '').toLowerCase() !== 'male' && (activeCustomer.gender || '').toLowerCase() !== 'female'
                                    }"
                                    x-text="activeCustomer.gender">
                                </span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-400 mt-0.5">
                            <template x-if="activeCustomer?.created_at">
                                <span>Joined <span x-text="activeCustomer.created_at"></span></span>
                            </template>
                            <template x-if="activeCustomer?.orders_count !== undefined">
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-medium">
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span x-text="activeCustomer.orders_count"></span> orders
                                </span>
                            </template>
                        </div>
                    </div>
                </div>

                <button type="button" @click="closeViewModal()" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/70 rounded-lg transition flex-shrink-0 cursor-pointer" title="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Scrollable Body --}}
            <div class="px-6 py-4 space-y-4 overflow-y-auto custom-scrollbar flex-1 text-sm">
                {{-- Contact Info Panel (2 columns) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-slate-50/90 p-3.5 rounded-xl border border-slate-200/70">
                    {{-- Phone --}}
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0 border border-emerald-100 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Phone</span>
                            <span class="font-semibold text-slate-800 text-xs truncate block" x-text="activeCustomer?.phone"></span>
                        </div>
                    </div>

                    {{-- Address --}}
                    <div class="flex items-start gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0 border border-indigo-100 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Address</span>
                            <p class="text-xs text-slate-700 break-words leading-relaxed" x-text="activeCustomer?.address || 'No address provided'"></p>
                        </div>
                    </div>
                </div>

                {{-- Notes (if any) --}}
                <template x-if="activeCustomer?.notes">
                    <div class="bg-amber-50/70 border border-amber-200/70 rounded-xl p-3">
                        <div class="flex items-center gap-1.5 text-amber-800 text-[11px] font-semibold uppercase tracking-wide mb-1">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                            Notes
                        </div>
                        <p class="text-xs text-amber-900 whitespace-pre-line leading-relaxed" x-text="activeCustomer.notes"></p>
                    </div>
                </template>

                {{-- Family Members Section --}}
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                            <span>Family Members</span>
                            <span class="bg-indigo-50 text-indigo-700 text-[11px] font-bold px-2 py-0.5 rounded-full border border-indigo-100" x-text="activeCustomer?.members?.length || 0"></span>
                        </span>
                    </div>

                    {{-- Members List: 3 cards per row --}}
                    <template x-if="activeCustomer?.members && activeCustomer.members.length > 0">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 max-h-56 overflow-y-auto custom-scrollbar pr-0.5">
                            <template x-for="m in activeCustomer.members" :key="m.id || m.name">
                                <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100/80 border border-slate-200/70 transition">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 shadow-sm"
                                        x-text="(m.name || '?').charAt(0).toUpperCase()">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-800 truncate" :title="m.name" x-text="m.name"></p>
                                        <div class="flex items-center justify-between gap-1 mt-0.5">
                                            <template x-if="m.gender">
                                                <span class="text-[10px] font-semibold px-1.5 py-0.2 rounded-full border capitalize leading-tight"
                                                    :class="{
                                                        'bg-blue-50 text-blue-700 border-blue-200': (m.gender || '').toLowerCase() === 'male',
                                                        'bg-pink-50 text-pink-700 border-pink-200': (m.gender || '').toLowerCase() === 'female',
                                                        'bg-slate-100 text-slate-600 border-slate-200': (m.gender || '').toLowerCase() !== 'male' && (m.gender || '').toLowerCase() !== 'female'
                                                    }"
                                                    x-text="m.gender">
                                                </span>
                                            </template>
                                            <template x-if="m.phone">
                                                <a :href="'tel:' + m.phone" class="text-[10px] text-slate-400 hover:text-emerald-600 truncate" :title="m.phone" x-text="m.phone"></a>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- No Members State --}}
                    <template x-if="!activeCustomer?.members || activeCustomer.members.length === 0">
                        <div class="text-center py-4 bg-slate-50/50 rounded-xl border border-dashed border-slate-200 text-xs text-slate-400">
                            No family members added
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer Actions --}}
            <div class="px-6 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" @click="closeViewModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 rounded-xl transition cursor-pointer">
                    Close
                </button>
                <a :href="activeCustomer?.edit_url" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm hover:shadow transition cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                    Edit Customer
                </a>
            </div>
        </div>
    </div>
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
@php
    $customersData = [];
    $membersData = [];
    foreach ($customers as $c) {
        $mList = [];
        foreach ($c->members as $m) {
            $mList[] = [
                'id'       => $m->id,
                'name'     => $m->name,
                'gender'   => $m->gender ? ucfirst($m->gender) : '',
                'relation' => $m->relation,
                'phone'    => $m->phone,
            ];
        }
        $membersData[$c->id] = $mList;
        $customersData[$c->id] = [
            'id'           => $c->id,
            'name'         => $c->name,
            'phone'        => $c->phone,
            'gender'       => $c->gender ? ucfirst($c->gender) : '',
            'address'      => $c->address,
            'notes'        => $c->notes,
            'created_at'   => $c->created_at ? $c->created_at->format('d M, Y') : '',
            'orders_count' => $c->orders_count ?? 0,
            'edit_url'     => route('customers.edit', $c),
            'members'      => $mList,
        ];
    }
@endphp
window.__members = @json($membersData);

function customerManager() {
    return {
        viewModalOpen: false,
        activeCustomer: null,
        customers: @json($customersData),
        openViewModal(id) {
            this.activeCustomer = this.customers[id] || null;
            this.viewModalOpen = true;
        },
        closeViewModal() {
            this.viewModalOpen = false;
        }
    };
}

function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
}

const X_SVG = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;display:block">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
    </svg>`;

const GENDER_CLS = {
    male:   'bg-blue-50 text-blue-700 border-blue-200',
    female: 'bg-pink-50 text-pink-700 border-pink-200',
    other:  'bg-slate-100 text-slate-600 border-slate-200',
};

function genderBadge(gender) {
    const cls = GENDER_CLS[gender] || GENDER_CLS.other;
    const label = gender ? escapeHtml(gender) : '—';
    return `<span class="text-xs font-semibold capitalize px-2.5 py-1 rounded-full border ${cls}">${label}</span>`;
}

function openSwal({ title, html, width = 380 }) {
    Swal.fire({
        title: title,
        html: html,
        showConfirmButton: false,
        showCloseButton: true,
        closeButtonHtml: X_SVG,
        width: width,
        customClass: { popup: 'rounded-2xl' },
    });
}

function membersListHtml(list) {
    const rows = list.map((m, i) => {
        let sub = [];
        if (m.relation) sub.push(escapeHtml(m.relation));
        if (m.phone) sub.push(escapeHtml(m.phone));
        return `
        <div class="flex items-center justify-between gap-3 px-3 py-2.5 rounded-lg border ${i % 2 ? 'bg-slate-50' : 'bg-white'} border-slate-100">
            <span class="flex items-center gap-2.5 min-w-0">
                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white text-sm font-bold flex items-center justify-center flex-shrink-0">${escapeHtml((m.name || '?').charAt(0).toUpperCase())}</span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-slate-700 truncate">${escapeHtml(m.name)}</span>
                    ${sub.length ? `<span class="block text-[11px] text-slate-400 truncate capitalize">${sub.join(' · ')}</span>` : ''}
                </span>
            </span>
            ${genderBadge(m.gender)}
        </div>`;
    }).join('');
    return `<div class="space-y-1.5 text-left max-h-80 overflow-y-auto pr-1">${rows}</div>`;
}

document.addEventListener('click', function (e) {
    const badge = e.target.closest('.js-members-badge');
    if (!badge) return;
    const list = (window.__members || {})[badge.dataset.customer] || [];
    if (!list.length) return;
    openSwal({ title: '<span class="text-lg font-bold text-slate-800">Members</span>', html: membersListHtml(list), width: 400 });
});
</script>
@endpush
