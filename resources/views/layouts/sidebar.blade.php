<?php

/**
 * Sidebar Blade component for the Tailor Management dashboard.
 * Includes links for Customer and Services sections with Spatie permission checks.
 */
?>
<div class="h-screen bg-gray-100 dark:bg-gray-900 w-64 flex flex-col">
    <div class="flex items-center justify-center h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">Tailor App</span>
    </div>
    <nav class="flex-1 px-2 py-4 space-y-2">
        @can('view-customers')
        <a href="{{ route('customers.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Customer
        </a>
        @endcan

        @can('view-orders')
        <a href="{{ route('orders.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-1.125 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Orders
        </a>
        @endcan

        @can('view-assign-orders')
        <a href="{{ route('assign-orders.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                    class="w-full group flex items-center justify-between px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
                <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                    </svg>
                    Vouchers
                </div>
                <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-1 pl-8 space-y-1">
                @can('view-payments')
                <a href="{{ route('payments.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Receipt Voucher
                </a>
                @endcan
                @can('manage-accounts')
                <a href="{{ route('expenses.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Expense Voucher
                </a>
                <a href="{{ route('expense-categories.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Expense Category
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        <!-- HR Collapsible Menu -->
        @canany(['manage-departments', 'manage-designations', 'manage-employees', 'manage-attendance', 'manage-salaries'])
        @php
            $isHrActive = request()->routeIs('departments.*', 'designations.*', 'employees.*', 'attendances.*', 'salaries.*', 'employee-ledger.*');
        @endphp
        <div x-data="{ open: {{ $isHrActive ? 'true' : 'false' }} }">
            <button @click="open = !open"
                    type="button"
                    class="w-full group flex items-center justify-between px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
                <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    HR
                </div>
                <svg class="h-4 w-4 transition-transform duration-200" 
                     :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                     style="{{ $isHrActive ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);' }} transition: transform 0.2s;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-1 pl-8 space-y-1">
                @can('manage-departments')
                <a href="{{ route('departments.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Department
                </a>
                @endcan
                @can('manage-designations')
                <a href="{{ route('designations.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Designation
                </a>
                @endcan
                @can('manage-employees')
                <a href="{{ route('employees.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Employees
                </a>
                @endcan
                @can('manage-attendance')
                <a href="{{ route('attendances.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Attendance
                </a>
                @endcan
                @can('manage-salaries')
                <a href="{{ route('salaries.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Pay Salary
                </a>
                @endcan
                @canany(['manage-employees', 'manage-salaries'])
                <a href="{{ route('employee-ledger.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
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
                    class="w-full group flex items-center justify-between px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
                <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    User Management
                </div>
                <svg class="h-4 w-4 transition-transform duration-200" 
                     :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"
                     style="{{ $isUserMgmtActive ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);' }} transition: transform 0.2s;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="open" x-cloak class="mt-1 pl-8 space-y-1">
                @can('view-users')
                <a href="{{ route('users.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Users
                </a>
                @endcan
                @can('view-roles')
                <a href="{{ route('roles.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Roles
                </a>
                @endcan
                @can('view-permissions')
                <a href="{{ route('permissions.index') }}"
                   class="block px-2 py-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    Permissions
                </a>
                @endcan
            </div>
        </div>
        @endcanany
    </nav>
</div>
