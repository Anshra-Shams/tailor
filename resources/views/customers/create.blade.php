@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="space-y-4 max-w-7xl mx-auto" x-data="customerForm()">

    {{-- Top Navigation & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Customers
                </a>
                <span>/</span>
                <span class="text-slate-400">New Customer</span>
            </div>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-sm shadow-indigo-500/25 flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-tight">Add New Customer</h2>
                    <p class="text-xs text-slate-500">Register customer account and optional family members</p>
                </div>
            </div>
        </div>

        {{-- Quick Header Actions --}}
        <div class="flex items-center gap-2.5">
            <a href="{{ route('customers.index') }}"
                class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition shadow-xs">
                Cancel
            </a>
            <button type="submit" form="customer-form"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-500/25 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                </svg>
                Save Customer
            </button>
        </div>
    </div>

    {{-- Validation Errors Banner --}}
    @if ($errors->any())
        <div class="bg-red-50/90 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-bold text-red-800">Please fix the following errors:</h4>
                <ul class="list-disc list-inside text-xs text-red-600 mt-0.5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Form (2-Column Grid Layout) --}}
    <form id="customer-form" method="POST" action="{{ route('customers.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        @csrf

        {{-- ================= LEFT COLUMN: MAIN CUSTOMER INFO (7 Cols) ================= --}}
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-4">
                {{-- Card Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 leading-tight">Primary Account Details</h3>
                            <p class="text-[11px] text-slate-400">Head of family or primary client</p>
                        </div>
                    </div>
                    <span class="text-[11px] text-slate-400 font-medium">* Required</span>
                </div>

                <div class="space-y-3.5">
                    {{-- Customer Name & Phone Number (2 Columns) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                                Customer Name <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                    class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="e.g. Muhammad Usman / Khan Family">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                    </svg>
                                </div>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                                    class="block w-full pl-9 pr-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                    placeholder="03XX-XXXXXXX">
                            </div>
                        </div>
                    </div>

                    {{-- Gender Segmented Pill Buttons --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">
                            Gender
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border text-xs font-semibold cursor-pointer select-none transition shadow-2xs"
                                :class="gender === 'male' ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="gender" value="male" x-model="gender" class="hidden">
                                <span>👨 Male</span>
                            </label>

                            <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border text-xs font-semibold cursor-pointer select-none transition shadow-2xs"
                                :class="gender === 'female' ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="gender" value="female" x-model="gender" class="hidden">
                                <span>👩 Female</span>
                            </label>

                            <label class="flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border text-xs font-semibold cursor-pointer select-none transition shadow-2xs"
                                :class="gender === 'other' ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-500/20' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100'">
                                <input type="radio" name="gender" value="other" x-model="gender" class="hidden">
                                <span>Other</span>
                            </label>
                        </div>
                    </div>

                    {{-- Address & Notes in 2 Columns --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                                    Address
                                </label>
                                <span class="text-[10px] text-slate-400">Optional</span>
                            </div>
                            <textarea id="address" name="address" rows="2"
                                class="block w-full px-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                                placeholder="Street, Colony, City...">{{ old('address') }}</textarea>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                                    Customer Notes
                                </label>
                                <span class="text-[10px] text-slate-400">Optional</span>
                            </div>
                            <textarea id="notes" name="notes" rows="2"
                                class="block w-full px-3 py-2 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"
                                placeholder="Special preferences or instructions...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Card Bottom Actions --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-[11px] text-slate-400">
                        Measurements can be added immediately after saving.
                    </p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('customers.index') }}"
                            class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-500/25 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Save Customer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= RIGHT COLUMN: FAMILY MEMBERS (5 Cols) ================= --}}
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 space-y-3.5">
                {{-- Card Header --}}
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">Family Members</h3>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="members.length > 0 ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-500'"
                                    x-text="members.length + ' Total'">
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400">Brothers, sons, or family profiles</p>
                        </div>
                    </div>

                    <button type="button" @click="addMember()"
                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold rounded-xl border border-purple-200 transition shadow-2xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Member
                    </button>
                </div>

                {{-- Members List with Contained Scrollbar (avoids whole-page scrolling) --}}
                <div x-show="members.length > 0" class="max-h-[290px] overflow-y-auto custom-scrollbar space-y-2.5 pr-1">
                    <template x-for="(member, index) in members" :key="index">
                        <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200 hover:border-purple-200 rounded-xl p-3 transition space-y-2 relative group" x-transition>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center text-[10px] font-bold" x-text="index + 1"></span>
                                    <span class="text-xs font-bold text-slate-700 truncate" x-text="member.name ? member.name : 'Member ' + (index + 1)"></span>
                                </div>
                                <button type="button" @click="removeMember(index)" title="Remove member"
                                    class="p-1 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                                <div class="sm:col-span-7">
                                    <input type="text" :name="'members[' + index + '][name]'" x-model="member.name" required
                                        class="block w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 transition"
                                        placeholder="Member Name *">
                                </div>
                                <div class="sm:col-span-5 flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-300">
                                    <label class="flex-1 inline-flex items-center justify-center py-0.5 px-1.5 rounded-md text-[11px] font-semibold cursor-pointer select-none transition"
                                        :class="member.gender === 'male' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'">
                                        <input type="radio" :name="'members[' + index + '][gender]'" value="male" x-model="member.gender" class="hidden">
                                        <span>Male</span>
                                    </label>
                                    <label class="flex-1 inline-flex items-center justify-center py-0.5 px-1.5 rounded-md text-[11px] font-semibold cursor-pointer select-none transition"
                                        :class="member.gender === 'female' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'">
                                        <input type="radio" :name="'members[' + index + '][gender]'" value="female" x-model="member.gender" class="hidden">
                                        <span>Female</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Empty State (Compact & Clean) --}}
                <div x-show="members.length === 0" class="rounded-xl border-2 border-dashed border-slate-200/80 p-5 text-center bg-slate-50/50">
                    <div class="w-9 h-9 mx-auto rounded-full bg-purple-50 text-purple-500 flex items-center justify-center mb-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <h5 class="text-xs font-bold text-slate-700">No Family Members</h5>
                    <p class="text-[11px] text-slate-400 mt-0.5 max-w-xs mx-auto">Click <strong class="text-purple-600 font-semibold cursor-pointer" @click="addMember()">"+ Add Member"</strong> above if this customer has family members sharing this account.</p>
                </div>

                {{-- Pro Tip Banner --}}
                <div class="rounded-xl bg-indigo-50/60 border border-indigo-100/80 p-3 flex items-start gap-2.5">
                    <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18" />
                        </svg>
                    </div>
                    <div class="text-[11px] leading-relaxed text-indigo-900">
                        <strong class="font-bold">Tailoring Tip:</strong> Family members share customer account and phone number, allowing separate body measurements for each person under one account.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function customerForm() {
    return {
        gender: '{{ old('gender', 'male') }}',
        members: @json(old('members', [])),
        addMember() {
            this.members.push({ name: '', gender: 'male' });
        },
        removeMember(index) {
            this.members.splice(index, 1);
        }
    }
}
</script>
@endpush
