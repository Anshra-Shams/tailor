<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Prowave') }} - @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Figtree', sans-serif;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        html::-webkit-scrollbar,
        body::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        /* Sleek modern vertical scrollbar for page content and custom scroll containers */
        main,
        .custom-scrollbar {
            scrollbar-width: thin !important;
            scrollbar-color: #cbd5e1 transparent !important;
        }
        main::-webkit-scrollbar,
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px !important;
            height: 8px !important;
            display: block !important;
        }
        main::-webkit-scrollbar-track,
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent !important;
        }
        main::-webkit-scrollbar-thumb,
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1 !important;
            border-radius: 9999px !important;
            border: 2px solid transparent !important;
            background-clip: padding-box !important;
        }
        main::-webkit-scrollbar-thumb:hover,
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8 !important;
        }

        /* Class to intentionally hide scrollbars where desired */
        .no-scrollbar {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
        }

        /* Dark Theme Sleek Scrollbar for Sidebar Navigation */
        #sidebar nav,
        aside nav,
        .sidebar-scroll {
            scrollbar-width: thin !important;
            scrollbar-color: #334155 transparent !important;
        }
        #sidebar nav::-webkit-scrollbar,
        aside nav::-webkit-scrollbar,
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px !important;
            height: 5px !important;
            display: block !important;
        }
        #sidebar nav::-webkit-scrollbar-track,
        aside nav::-webkit-scrollbar-track,
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent !important;
        }
        #sidebar nav::-webkit-scrollbar-thumb,
        aside nav::-webkit-scrollbar-thumb,
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background-color: #334155 !important;
            border-radius: 9999px !important;
        }
        #sidebar nav::-webkit-scrollbar-thumb:hover,
        aside nav::-webkit-scrollbar-thumb:hover,
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background-color: #6366f1 !important;
        }
        .sidebar-link.active { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; box-shadow: 0 4px 12px rgba(99,102,241,0.3); }
        .sidebar-link:hover:not(.active) { background: #f1f5f9; color: #1e293b; }
        /* Hide Alpine.js elements before init to prevent flash */
        [x-cloak] { display: none !important; }
        /* Remove browser native clear/refresh/cancel buttons from inputs */
        input::-webkit-search-cancel-button,
        input::-webkit-search-results-button,
        input::-webkit-search-decoration { display: none; -webkit-appearance: none; }
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="antialiased bg-slate-50">
    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-72 bg-slate-900 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl lg:shadow-none">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-6 h-16 border-b border-slate-700/50 flex-shrink-0 group">
                @if(!empty($appSettings?->logo_url))
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700/60 p-1 flex items-center justify-center overflow-hidden shrink-0 shadow-md">
                        <img src="{{ $appSettings->logo_url }}" alt="{{ $appSettings->company_name ?? 'Logo' }}" class="w-full h-full object-contain" />
                    </div>
                @else
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 shadow-lg shadow-indigo-500/30 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
                        </svg>
                    </div>
                @endif
                <div class="min-w-0">
                    <span class="text-xl font-bold text-white tracking-tight truncate block">{{ $appSettings->company_name ?? 'Prowave' }}</span>
                    <span class="block text-[11px] font-medium text-slate-400 -mt-0.5 tracking-wider uppercase truncate">Tailor Management System</span>
                </div>
            </a>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </a>

                @can('view-services')
                <a href="{{ route('services.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('services.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17l-5.384 3.18A1.5 1.5 0 014 17.08V5.92a1.5 1.5 0 012.036-1.42l5.384 3.18a1.5 1.5 0 010 2.58z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" />
                    </svg>
                    Services
                </a>
                @endcan

                @can('view-customers')
                <a href="{{ route('customers.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('customers.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Customers
                </a>
                @endcan

                @can('view-measurements')
                <a href="{{ route('measurements.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('measurements.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    Measurements
                </a>
                @endcan

                @can('view-orders')
                <a href="{{ route('orders.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('orders.*') && !request()->routeIs('assign-orders.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-1.125 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    Orders
                </a>
                @endcan

                @can('view-assign-orders')
                <a href="{{ route('assign-orders.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('assign-orders.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                    Assign Order
                </a>
                @endcan

                <!-- Vouchers Collapsible Menu -->
                @canany(['view-payments', 'manage-accounts'])
                @php
                    $isVouchersActive = request()->routeIs('payments.*', 'expenses.*', 'expense-categories.*');
                @endphp
                <div x-data="{ open: {{ $isVouchersActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            type="button"
                            class="sidebar-link w-full flex items-center justify-between px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 cursor-pointer {{ $isVouchersActive ? 'active' : 'text-slate-400' }}">
                        <div class="flex items-center gap-3.5">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                            </svg>
                            <span>Vouchers</span>
                        </div>
                        <svg id="vouchers-chevron" 
                             class="w-4 h-4 transition-transform duration-200" 
                             :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                             style="{{ $isVouchersActive ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);' }} transition: transform 0.2s ease;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="vouchers-submenu"
                         x-show="open"
                         x-cloak
                         class="mt-1 pl-9 pr-2 space-y-1">
                        @can('view-payments')
                        <a href="{{ route('payments.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('payments.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Receipt Voucher
                        </a>
                        @endcan
                        @can('manage-accounts')
                        <a href="{{ route('expenses.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('expenses.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Expense Voucher
                        </a>
                        <a href="{{ route('expense-categories.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('expense-categories.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Expense Category
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                @can('view-accounts')
                <a href="{{ route('accounts.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('accounts.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                    Chart of Accounts
                </a>
                @endcan

                @can('view-reports')
                <a href="{{ route('reports.index') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Reports
                </a>
                @endcan

                <!-- HR Collapsible Menu -->
                @canany(['manage-departments', 'manage-designations', 'manage-employees', 'manage-attendance', 'manage-salaries'])
                @php
                    $isHrActive = request()->routeIs('departments.*', 'designations.*', 'employees.*', 'attendances.*', 'salaries.*', 'employee-ledger.*');
                @endphp
                <div x-data="{ open: {{ $isHrActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            type="button"
                            class="sidebar-link w-full flex items-center justify-between px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 cursor-pointer {{ $isHrActive ? 'active' : 'text-slate-400' }}">
                        <div class="flex items-center gap-3.5">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>HR</span>
                        </div>
                        <svg id="hr-chevron" 
                             class="w-4 h-4 transition-transform duration-200" 
                             :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                             style="{{ $isHrActive ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);' }} transition: transform 0.2s ease;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="hr-submenu"
                         x-show="open"
                         x-cloak
                         class="mt-1 pl-9 pr-2 space-y-1">
                        @can('manage-departments')
                        <a href="{{ route('departments.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('departments.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Department
                        </a>
                        @endcan
                        @can('manage-designations')
                        <a href="{{ route('designations.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('designations.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Designation
                        </a>
                        @endcan
                        @can('manage-employees')
                        <a href="{{ route('employees.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('employees.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Employees
                        </a>
                        @endcan
                        @can('manage-attendance')
                        <a href="{{ route('attendances.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('attendances.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Attendance
                        </a>
                        @endcan
                        @can('manage-salaries')
                        <a href="{{ route('salaries.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('salaries.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Pay Salary
                        </a>
                        @endcan
                        @canany(['manage-employees', 'manage-salaries'])
                        <a href="{{ route('employee-ledger.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('employee-ledger.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Employee Ledger
                        </a>
                        @endcanany
                    </div>
                </div>
                @endcanany

                <!-- User Management Collapsible Menu -->
                @canany(['view-users', 'view-roles', 'view-permissions'])
                @php
                    $isUserMgmtActive = request()->routeIs('users.*', 'roles.*', 'permissions.*');
                @endphp
                <div x-data="{ open: {{ $isUserMgmtActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            type="button"
                            class="sidebar-link w-full flex items-center justify-between px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 cursor-pointer {{ $isUserMgmtActive ? 'active' : 'text-slate-400' }}">
                        <div class="flex items-center gap-3.5">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                            </svg>
                            <span>User Management</span>
                        </div>
                        <svg id="usermgmt-chevron" 
                             class="w-4 h-4 transition-transform duration-200" 
                             :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                             style="{{ $isUserMgmtActive ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);' }} transition: transform 0.2s ease;"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div id="usermgmt-submenu"
                         x-show="open"
                         x-cloak
                         class="mt-1 pl-9 pr-2 space-y-1">
                        @can('view-users')
                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('users.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Users
                        </a>
                        @endcan
                        @can('view-roles')
                        <a href="{{ route('roles.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('roles.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Roles
                        </a>
                        @endcan
                        @can('view-permissions')
                        <a href="{{ route('permissions.index') }}"
                           class="flex items-center px-3.5 py-2 text-[14.5px] font-medium rounded-lg transition-colors {{ request()->routeIs('permissions.*') ? 'text-white bg-slate-800 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                            Permissions
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                @can('manage-settings')
                <a href="{{ route('settings.edit') }}" class="sidebar-link flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium rounded-xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'active' : 'text-slate-400' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
                @endcan

            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    @yield('back_button')
                    <h1 class="text-lg font-bold text-slate-800">@yield('title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:block text-sm text-slate-500">{{ now()->format('l, F j, Y') }}</span>
                    <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>
                    
                    <!-- User Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" 
                                type="button" 
                                class="flex items-center gap-2 p-1 sm:px-2 sm:py-1 rounded-xl hover:bg-slate-100 transition-all focus:outline-none"
                                title="User Menu">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-xs shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden md:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-1.5 z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="text-xs font-medium text-slate-400">Signed in as</p>
                                <p class="text-sm font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="pt-1">
                                @can('manage-settings')
                                <a href="{{ route('settings.edit') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition font-medium text-left">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Settings
                                </a>
                                @endcan
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition font-medium text-left">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Direct Quick Logout Button -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                title="Logout">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        {{ session('success') }}
                        <button onclick="this.parentElement.remove()" class="ml-auto"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                        <button onclick="this.parentElement.remove()" class="ml-auto"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" /></svg></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function toggleVouchersSubmenu() {
            const submenu = document.getElementById('vouchers-submenu');
            const chevron = document.getElementById('vouchers-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
            }
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        function toggleHrSubmenu() {
            const submenu = document.getElementById('hr-submenu');
            const chevron = document.getElementById('hr-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
            }
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        function toggleUserMgmtSubmenu() {
            const submenu = document.getElementById('usermgmt-submenu');
            const chevron = document.getElementById('usermgmt-chevron');
            if (submenu) {
                submenu.classList.toggle('hidden');
            }
            if (chevron) {
                chevron.classList.toggle('rotate-180');
            }
        }

        // Global SweetAlert confirmation for all delete forms (.js-delete-form)
        document.addEventListener('submit', function (e) {
            const form = e.target.closest('form.js-delete-form');
            if (!form) return;
            e.preventDefault();
            if (typeof Swal === 'undefined') { form.submit(); return; }
            const name = form.dataset.name || '';
            Swal.fire({
                title: form.dataset.title || 'Are you sure?',
                html: name
                    ? `"<strong>${name}</strong>" will be permanently deleted.<br>This action cannot be undone.`
                    : 'This record will be permanently deleted.<br>This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
