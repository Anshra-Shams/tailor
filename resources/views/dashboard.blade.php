@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-500/20">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="text-indigo-100 mt-1">Here's what's happening with your tailor shop today.</p>
            </div>
            <div class="text-5xl opacity-30">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <!-- Total Customers -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Customers</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $totalCustomers }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('customers.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center gap-1">
                    View all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- Total Services -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Services</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $totalServices }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('services.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-medium inline-flex items-center gap-1">
                    View all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- New Orders -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Pending Orders</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $newOrders }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-slate-500">Orders awaiting completion</span>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('customers.create') }}" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Add Customer</p>
                    <p class="text-sm text-slate-500">Register a new customer</p>
                </div>
            </a>
            <a href="{{ route('services.create') }}" class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 group">
                <div class="w-12 h-12 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Add Service</p>
                    <p class="text-sm text-slate-500">Create a new service</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
