<?php

// ─────────────────────────────────────────────
// 1. DASHBOARD LAYOUT (layouts/dashboard-layout.blade.php)
// ─────────────────────────────────────────────
$dashboardLayout = '<!DOCTYPE html>
<html lang="{{ str_replace(\'_\', \'-\', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config(\'app.name\', \'Tailor App\') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite([\'resources/css/app.css\', \'resources/js/app.js\'])
    <style>
        [x-cloak]{display:none!important}
        .sidebar-active{background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);color:#fff!important;box-shadow:0 4px 15px rgba(79,70,229,.35)}
        .sidebar-active svg{color:#fff!important}
    </style>
</head>
<body class="bg-gray-50 antialiased font-sans">
<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: true, activeMenu: \'dashboard\' }">

    {{-- ========== SIDEBAR ========== --}}
    <aside :class="sidebarOpen ? \'w-64\' : \'w-20\'"
           class="relative z-30 flex flex-col h-screen transition-all duration-300 ease-in-out bg-gradient-to-b from-gray-900 via-gray-900 to-indigo-950 shadow-2xl overflow-hidden">

        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600 opacity-10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-20 left-0 w-24 h-24 bg-purple-600 opacity-10 rounded-full translate-y-1/2 -translate-x-1/2 blur-3xl pointer-events-none"></div>

        {{-- Branding --}}
        <div class="flex items-center px-5 py-5 border-b border-white/10">
            <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>
                </svg>
            </div>
            <div x-show="sidebarOpen" x-transition class="ml-3 overflow-hidden">
                <h1 class="text-white font-bold text-lg leading-tight tracking-wide">TailorApp</h1>
                <p class="text-indigo-300 text-xs font-medium">Management System</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <p x-show="sidebarOpen" class="text-gray-500 text-xs font-semibold uppercase tracking-widest px-3 mb-2">Main Menu</p>

            {{-- Dashboard --}}
            <a href="{{ route(\'dashboard\') }}" @click="activeMenu = \'dashboard\'"
               :class="activeMenu === \'dashboard\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
            </a>

            {{-- Customers --}}
            <a href="#" @click="activeMenu = \'customers\'"
               :class="activeMenu === \'customers\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Customers</span>
                <span x-show="sidebarOpen" class="ml-auto bg-indigo-500/20 text-indigo-300 text-xs font-semibold px-2 py-0.5 rounded-full">0</span>
            </a>

            {{-- Services --}}
            <a href="#" @click="activeMenu = \'services\'"
               :class="activeMenu === \'services\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Services</span>
            </a>

            {{-- Orders --}}
            <a href="#" @click="activeMenu = \'orders\'"
               :class="activeMenu === \'orders\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Orders</span>
                <span x-show="sidebarOpen" class="ml-auto bg-yellow-500/20 text-yellow-300 text-xs font-semibold px-2 py-0.5 rounded-full">New</span>
            </a>

            <p x-show="sidebarOpen" class="text-gray-500 text-xs font-semibold uppercase tracking-widest px-3 mb-2 mt-4">Reports</p>

            {{-- Reports --}}
            <a href="#" @click="activeMenu = \'reports\'"
               :class="activeMenu === \'reports\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Reports</span>
            </a>

            {{-- Settings --}}
            <a href="{{ route(\'profile.edit\') }}" @click="activeMenu = \'settings\'"
               :class="activeMenu === \'settings\' ? \'sidebar-active\' : \'text-gray-400 hover:bg-white/10 hover:text-white\'"
               class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-medium transition-all duration-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="sidebarOpen" class="whitespace-nowrap">Settings</span>
            </a>
        </nav>

        {{-- Sidebar Footer --}}
        <div class="px-3 py-4 border-t border-white/10">
            <div class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-white/5 transition-all duration-200">
                <div class="flex-shrink-0 w-9 h-9 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold shadow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div x-show="sidebarOpen" class="overflow-hidden flex-1">
                    <p class="text-white text-sm font-semibold truncate">{{ Auth::user()->name }}</p>
                    <p class="text-gray-400 text-xs truncate">Administrator</p>
                </div>
                <div x-show="sidebarOpen">
                    <form method="POST" action="{{ route(\'logout\') }}">
                        @csrf
                        <button type="submit" title="Logout" class="text-gray-400 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- Top Navbar --}}
        <header class="flex items-center justify-between h-16 px-6 bg-white border-b border-gray-200 shadow-sm z-20">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                @isset($pageTitle)
                <div class="flex items-center gap-2 text-sm">
                    <span class="text-gray-300">/</span>
                    <span class="font-semibold text-gray-800">{{ $pageTitle }}</span>
                </div>
                @endisset
            </div>

            <div class="flex items-center gap-3">
                <button class="relative p-2 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
                <div class="h-6 w-px bg-gray-200"></div>
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl hover:bg-gray-100 transition-all duration-200">
                        <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-gray-800 leading-tight">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">Admin</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open ? \'rotate-180\' : \'\'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route(\'profile.edit\') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            My Profile
                        </a>
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="{{ route(\'logout\') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>';

file_put_contents(
    __DIR__ . '/resources/views/layouts/dashboard-layout.blade.php',
    $dashboardLayout
);

// ─────────────────────────────────────────────
// 2. DASHBOARD VIEW (dashboard.blade.php)
// ─────────────────────────────────────────────
$dashboardView = '<x-dashboard-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">0</p>
                <p class="text-sm text-gray-500 font-medium">Total Customers</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">0</p>
                <p class="text-sm text-gray-500 font-medium">Active Services</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">0</p>
                <p class="text-sm text-gray-500 font-medium">Total Orders</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-800">Rs 0</p>
                <p class="text-sm text-gray-500 font-medium">Revenue</p>
            </div>
        </div>
    </div>

    {{-- Welcome Banner --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white mb-8 shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse"><path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(#grid)"/></svg>
        </div>
        <div class="relative">
            <h2 class="text-2xl font-bold mb-1">Welcome back, {{ Auth::user()->name }}! 👋</h2>
            <p class="text-indigo-100 text-sm">Here is what is happening with your tailor shop today.</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button class="flex flex-col items-center gap-2 px-4 py-4 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span class="text-xs font-semibold">Add Customer</span>
            </button>
            <button class="flex flex-col items-center gap-2 px-4 py-4 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                <span class="text-xs font-semibold">New Order</span>
            </button>
            <button class="flex flex-col items-center gap-2 px-4 py-4 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-xs font-semibold">Add Service</span>
            </button>
            <button class="flex flex-col items-center gap-2 px-4 py-4 bg-green-50 hover:bg-green-100 text-green-700 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-xs font-semibold">View Reports</span>
            </button>
        </div>
    </div>
</x-dashboard-layout>';

file_put_contents(
    __DIR__ . '/resources/views/dashboard.blade.php',
    $dashboardView
);

// ─────────────────────────────────────────────
// 3. LOGIN VIEW (auth/login.blade.php)
// ─────────────────────────────────────────────
$loginView = '<x-guest-layout>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-950 via-gray-900 to-purple-950 relative overflow-hidden">

    {{-- Background decorations --}}
    <div class="absolute top-0 left-0 w-96 h-96 bg-indigo-600 opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-600 opacity-10 rounded-full translate-x-1/2 translate-y-1/2 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md px-6 py-10">

        {{-- Logo & Title --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-2xl mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">TailorApp</h1>
            <p class="text-indigo-300 text-sm mt-1">Sign in to your dashboard</p>
        </div>

        {{-- Card --}}
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="mb-5 p-4 bg-red-500/20 border border-red-400/30 rounded-xl">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-red-300 text-sm font-semibold">Please fix the following errors:</p>
                </div>
                <ul class="text-red-300 text-sm list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Session Status --}}
            @if (session(\'status\'))
            <div class="mb-5 p-4 bg-green-500/20 border border-green-400/30 rounded-xl">
                <p class="text-green-300 text-sm">{{ session(\'status\') }}</p>
            </div>
            @endif

            <form method="POST" action="{{ route(\'login\') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-indigo-200 mb-1.5">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old(\'email\') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all duration-200 @error(\'email\') border-red-400 @enderror"
                               placeholder="superadmin@example.com">
                    </div>
                    @error(\'email\')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-indigo-200 mb-1.5">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required
                               class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-indigo-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent transition-all duration-200 @error(\'password\') border-red-400 @enderror"
                               placeholder="••••••••">
                    </div>
                    @error(\'password\')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 text-indigo-500 focus:ring-indigo-400 focus:ring-offset-0">
                        <span class="text-sm text-indigo-200">Remember me</span>
                    </label>
                    @if (Route::has(\'password.request\'))
                    <a href="{{ route(\'password.request\') }}" class="text-sm text-indigo-300 hover:text-white transition-colors duration-200">Forgot password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/30 transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-transparent">
                    Sign In to Dashboard
                </button>
            </form>
        </div>

        <p class="text-center text-indigo-400 text-xs mt-6">&copy; {{ date(\'Y\') }} TailorApp. All rights reserved.</p>
    </div>
</div>
</x-guest-layout>';

file_put_contents(
    __DIR__ . '/resources/views/auth/login.blade.php',
    $loginView
);

echo "ALL FILES WRITTEN SUCCESSFULLY\n";
echo "1. layouts/dashboard-layout.blade.php\n";
echo "2. dashboard.blade.php\n";
echo "3. auth/login.blade.php\n";
