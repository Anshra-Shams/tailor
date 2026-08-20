<?php

/**
 * Sidebar Blade component for the Tailor Management dashboard.
 * Includes links for Customer and Services sections.
 */
?>
<div class="h-screen bg-gray-100 dark:bg-gray-900 w-64 flex flex-col">
    <div class="flex items-center justify-center h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <span class="text-xl font-semibold text-gray-800 dark:text-gray-200">Tailor App</span>
    </div>
    <nav class="flex-1 px-2 py-4 space-y-2">
        <a href="{{ route('customers.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Customer
        </a>
        <a href="{{ route('services.index') }}"
           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
            <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M9 8h6" />
            </svg>
            Services
        </a>
    </nav>
</div>
