@extends('layouts.app')

@section('title', $customer->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <a href="{{ route('customers.index') }}" class="inline-flex items-center gap-1 text-sm text-slate-500 hover:text-indigo-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Back to Customers
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                    {{ substr($customer->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $customer->name }}</h2>
                    <p class="text-slate-500">Customer since {{ $customer->created_at->format('M Y') }}</p>
                </div>
            </div>
            <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-indigo-50 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-100 transition-all text-sm self-start">Edit</a>
        </div>
    </div>

    <!-- Main Customer Info -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center gap-3 pb-4 border-b border-slate-100 mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Main Customer</h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg></div>
                <div><p class="text-xs text-slate-500">Phone</p><p class="text-sm font-medium text-slate-800">{{ $customer->phone }}</p></div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" /></svg></div>
                <div><p class="text-xs text-slate-500">Gender</p><p class="text-sm font-medium text-slate-800">{{ $customer->gender ? ucfirst($customer->gender) : '—' }}</p></div>
            </div>
            <div class="flex items-center gap-3 sm:col-span-2">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center"><svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg></div>
                <div><p class="text-xs text-slate-500">Address</p><p class="text-sm font-medium text-slate-800">{{ $customer->address ?: 'Not provided' }}</p></div>
            </div>
        </div>
        @if($customer->notes)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-500 mb-1">Notes</p>
                <p class="text-sm text-slate-700">{{ $customer->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Members Section -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Family Members</h3>
                    <p class="text-sm text-slate-500">{{ $customer->members->count() }} member{{ $customer->members->count() !== 1 ? 's' : '' }}</p>
                </div>
            </div>
        </div>

        @if($customer->members->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($customer->members as $member)
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 hover:border-purple-300 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                {{ substr($member->name, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $member->name }}</p>
                                <span class="text-xs {{ $member->gender === 'female' ? 'text-pink-500' : 'text-blue-500' }}">{{ ucfirst($member->gender) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                <p class="text-sm text-slate-500">No family members added yet.</p>
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-700 font-medium mt-2">Add Members →</a>
            </div>
        @endif
    </div>
</div>
@endsection
