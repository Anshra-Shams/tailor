@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="space-y-6" x-data="chartOfAccounts()">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Chart of Accounts</h2>
            <p class="text-slate-500 mt-1">Manage your financial account categories and heads</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @click="openCategoryModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Category
            </button>
            <button type="button" @click="openAccountModal()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Add Account
            </button>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Empty state --}}
        <div x-show="categories.length === 0" class="px-6 py-16 text-center">
            <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-indigo-50 flex items-center justify-center">
                <svg class="w-7 h-7 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/>
                </svg>
            </div>
            <h4 class="text-sm font-semibold text-slate-600">No categories yet</h4>
            <p class="text-xs text-slate-400 mt-1">Click "Add Category" to get started.</p>
        </div>

        <div x-show="categories.length > 0" class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3 w-44">Category</th>
                        <th class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Account Name</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3">Current Balance</th>
                        <th class="text-center text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 hidden md:table-cell">Type</th>
                        <th class="text-center text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 hidden sm:table-cell">Status</th>
                        <th class="text-right text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <template x-for="cat in categories" :key="cat.id">
                    <tbody class="divide-y divide-slate-50">
                        {{-- Category row --}}
                        <tr class="border-t border-slate-100 bg-indigo-50/50">
                            <td class="px-5 py-2.5" colspan="5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500 flex-shrink-0"></span>
                                    <span class="font-bold text-indigo-700 text-sm" x-text="cat.name"></span>
                                    <span class="text-xs text-slate-400" x-show="cat.description" x-text="'— ' + cat.description"></span>
                                </div>
                            </td>
                            <td class="px-5 py-2.5 text-right">
                                <button type="button" @click="openEditCategoryModal(cat)"
                                    class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors" title="Edit category">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>

                        {{-- No accounts --}}
                        <tr x-show="cat.accounts.length === 0">
                            <td colspan="6" class="px-5 py-4 text-center">
                                <span class="text-xs text-slate-400 italic">No accounts under this category — </span>
                                <button type="button" @click="openAccountModalFor(cat)"
                                    class="text-xs text-indigo-500 hover:text-indigo-700 font-semibold transition">add one</button>
                            </td>
                        </tr>

                        {{-- Account rows --}}
                        <template x-for="acc in cat.accounts" :key="acc.id">
                            <tr class="hover:bg-slate-50/60 transition-colors"
                                :class="!acc.is_active ? 'opacity-55' : ''">
                                <td class="px-5 py-3 text-slate-400 text-xs italic" x-text="cat.name"></td>
                                <td class="px-4 py-3">
                                    <span class="font-semibold text-slate-800 text-sm" x-text="acc.name"></span>
                                    <span class="block text-[11px] text-slate-400 mt-0.5" x-text="'Opening: ' + money(acc.opening_balance)"></span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-bold text-slate-700" x-text="money(acc.current_balance)"></span>
                                </td>
                                <td class="px-4 py-3 text-center hidden md:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                        :class="acc.type === 'debit' ? 'bg-slate-800 text-white' : 'bg-indigo-600 text-white'"
                                        x-text="acc.type"></span>
                                </td>
                                <td class="px-4 py-3 text-center hidden sm:table-cell">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                        :class="acc.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                        x-text="acc.is_active ? 'Active' : 'Inactive'"></span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Edit --}}
                                        <button type="button" @click="openEditAccountModal(acc)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                            </svg>
                                            Edit
                                        </button>
                                        {{-- Ledger --}}
                                        <a :href="'/chart-of-accounts/' + acc.id + '/ledger'"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                            </svg>
                                            Ledger
                                        </a>
                                        {{-- Deactivate / Activate --}}
                                        <button type="button" @click="toggleAccount(acc)"
                                            :disabled="togglingId === acc.id"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold rounded-lg border transition-all disabled:opacity-50"
                                            :class="acc.is_active
                                                ? 'text-red-600 border-red-200 bg-red-50 hover:bg-red-100'
                                                : 'text-emerald-700 border-emerald-200 bg-emerald-50 hover:bg-emerald-100'">
                                            <svg x-show="!acc.is_active" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 5.636a9 9 0 1012.728 0M12 3v9"/>
                                            </svg>
                                            <svg x-show="acc.is_active" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                            <span x-text="acc.is_active ? 'Deactivate' : 'Activate'"></span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </template>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ADD / EDIT CATEGORY MODAL
    ═══════════════════════════════════════════════════ --}}
    <div x-show="catModalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeCategoryModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800" x-text="editingCat ? 'Edit Category' : 'Add Category'"></h3>
                </div>
                <button type="button" @click="closeCategoryModal()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="catForm.name" autocomplete="off" spellcheck="false"
                        placeholder="e.g. Bank & Cash, Assets, Liabilities..."
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        :class="catError ? 'border-red-400 bg-red-50' : ''">
                    <p x-show="catError" x-text="catError" class="mt-1.5 text-xs text-red-600"></p>
                    <p x-show="!editingCat" class="mt-1.5 text-[11px] text-slate-400">Each category name must be unique — cannot be repeated.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Description (optional)</label>
                    <textarea x-model="catForm.description" rows="3"
                        placeholder="Optional description..."
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition resize-none"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="closeCategoryModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Cancel</button>
                <button type="button" @click="saveCategory()" :disabled="catSaving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed rounded-xl shadow-sm transition">
                    <svg x-show="catSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="catSaving ? 'Saving...' : (editingCat ? 'Update Category' : 'Create Category')"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         ADD ACCOUNT MODAL
    ═══════════════════════════════════════════════════ --}}
    <div x-show="accModalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeAccountModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Add Account</h3>
                </div>
                <button type="button" @click="closeAccountModal()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-4">
                {{-- Category select --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Account Category <span class="text-red-500">*</span>
                    </label>
                    <select x-model="accForm.category_id"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        :class="accErrors.category_id ? 'border-red-400 bg-red-50' : ''">
                        <option value="">— Select Category —</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                    <p x-show="accErrors.category_id" x-text="accErrors.category_id" class="mt-1 text-xs text-red-600"></p>
                </div>

                {{-- Account name --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Account Name / Head <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="accForm.name" autocomplete="off" spellcheck="false"
                        placeholder="e.g. HBL Bank, Petty Cash..."
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        :class="accErrors.name ? 'border-red-400 bg-red-50' : ''">
                    <p x-show="accErrors.name" x-text="accErrors.name" class="mt-1 text-xs text-red-600"></p>
                </div>

                {{-- Opening balance --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Opening Balance</label>
                    <input type="number" x-model="accForm.opening_balance" min="0" step="0.01"
                        placeholder="0.00"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>

                {{-- Balance type --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                        Balance Type <span class="text-red-500">*</span>
                    </label>
                    <select x-model="accForm.type"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        :class="accErrors.type ? 'border-red-400 bg-red-50' : ''">
                        <option value="">— Select Type —</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                    <p x-show="accErrors.type" x-text="accErrors.type" class="mt-1 text-xs text-red-600"></p>
                </div>

                <p x-show="accErrors.general" x-text="accErrors.general" class="text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"></p>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="closeAccountModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Cancel</button>
                <button type="button" @click="saveAccount()" :disabled="accSaving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 disabled:bg-slate-300 disabled:cursor-not-allowed rounded-xl shadow-sm transition">
                    <svg x-show="accSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="accSaving ? 'Saving...' : 'Create Account'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         EDIT ACCOUNT MODAL
    ═══════════════════════════════════════════════════ --}}
    <div x-show="editAccModalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="closeEditAccountModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Edit Account</h3>
                </div>
                <button type="button" @click="closeEditAccountModal()" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Category</label>
                    <select x-model="editAccForm.category_id"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Account Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="editAccForm.name" autocomplete="off"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        :class="editAccError ? 'border-red-400 bg-red-50' : ''">
                    <p x-show="editAccError" x-text="editAccError" class="mt-1 text-xs text-red-600"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Opening Balance</label>
                    <input type="number" x-model="editAccForm.opening_balance" min="0" step="0.01"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Balance Type <span class="text-red-500">*</span></label>
                    <select x-model="editAccForm.type"
                        class="w-full py-2.5 px-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                        <option value="">— Select Type —</option>
                        <option value="debit">Debit</option>
                        <option value="credit">Credit</option>
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" @click="closeEditAccountModal()"
                    class="px-4 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Cancel</button>
                <button type="button" @click="saveEditAccount()" :disabled="editAccSaving"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 disabled:cursor-not-allowed rounded-xl shadow-sm transition">
                    <svg x-show="editAccSaving" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span x-text="editAccSaving ? 'Saving...' : 'Update Account'"></span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
const TOKEN = document.querySelector('meta[name="csrf-token"]').content;

function chartOfAccounts() {
    return {
        categories: @json($categoriesData),

        // ── Category modal ──
        catModalOpen: false,
        catForm: { name: '', description: '' },
        catError: '',
        catSaving: false,
        editingCat: null,

        // ── Account modal ──
        accModalOpen: false,
        accForm: { category_id: '', name: '', opening_balance: 0, type: '' },
        accErrors: {},
        accSaving: false,

        // ── Toggle ──
        togglingId: null,

        // ── Edit Account modal ──
        editAccModalOpen: false,
        editingAcc: null,
        editAccForm: { category_id: '', name: '', opening_balance: 0, type: 'debit' },
        editAccError: '',
        editAccSaving: false,

        // ────────────────────────────────
        // STATS
        // ────────────────────────────────
        totalAccounts() {
            return this.categories.reduce((n, c) => n + c.accounts.length, 0);
        },
        activeAccounts() {
            return this.categories.reduce((n, c) => n + c.accounts.filter(a => a.is_active).length, 0);
        },
        inactiveAccounts() {
            return this.categories.reduce((n, c) => n + c.accounts.filter(a => !a.is_active).length, 0);
        },

        // ────────────────────────────────
        // CATEGORY MODAL
        // ────────────────────────────────
        openCategoryModal() {
            this.editingCat = null;
            this.catForm = { name: '', description: '' };
            this.catError = '';
            this.catModalOpen = true;
        },
        openEditCategoryModal(cat) {
            this.editingCat = cat;
            this.catForm = { name: cat.name, description: cat.description || '' };
            this.catError = '';
            this.catModalOpen = true;
        },
        closeCategoryModal() {
            this.catModalOpen = false;
            this.editingCat = null;
            this.catForm = { name: '', description: '' };
            this.catError = '';
        },

        async saveCategory() {
            this.catError = '';
            if (!this.catForm.name.trim()) {
                this.catError = 'Category name is required.';
                return;
            }
            this.catSaving = true;
            try {
                const isEdit = !!this.editingCat;
                const url = isEdit
                    ? `/chart-of-accounts/categories/${this.editingCat.id}`
                    : '/chart-of-accounts/categories';
                const method = isEdit ? 'PATCH' : 'POST';

                const fd = new FormData();
                fd.append('name', this.catForm.name.trim());
                fd.append('description', this.catForm.description.trim());
                if (isEdit) fd.append('_method', 'PATCH');

                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: fd,
                });
                const d = await res.json();

                if (res.ok && d.success) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: d.message });

                    if (isEdit) {
                        const idx = this.categories.findIndex(c => c.id === this.editingCat.id);
                        if (idx !== -1) {
                            this.categories[idx].name = d.category.name;
                            this.categories[idx].description = d.category.description;
                        }
                    } else {
                        this.categories.push({ ...d.category, accounts: [] });
                        this.categories.sort((a, b) => a.name.localeCompare(b.name));
                    }
                    this.closeCategoryModal();
                } else {
                    const errors = d.errors;
                    if (errors?.name) {
                        this.catError = errors.name[0];
                    } else {
                        this.catError = d.message || 'Could not save category.';
                    }
                }
            } catch (e) {
                this.catError = 'Network error. Please try again.';
            }
            this.catSaving = false;
        },

        // ────────────────────────────────
        // ACCOUNT MODAL
        // ────────────────────────────────
        openAccountModal() {
            this.accForm = { category_id: '', name: '', opening_balance: 0, type: '' };
            this.accErrors = {};
            this.accModalOpen = true;
        },
        openAccountModalFor(cat) {
            this.accForm = { category_id: cat.id, name: '', opening_balance: 0, type: '' };
            this.accErrors = {};
            this.accModalOpen = true;
        },
        closeAccountModal() {
            this.accModalOpen = false;
            this.accForm = { category_id: '', name: '', opening_balance: 0, type: '' };
            this.accErrors = {};
        },

        async saveAccount() {
            this.accErrors = {};
            if (!this.accForm.category_id) { this.accErrors.category_id = 'Please select a category.'; return; }
            if (!this.accForm.name.trim()) { this.accErrors.name = 'Account name is required.'; return; }
            if (!this.accForm.type) { this.accErrors.type = 'Please select a balance type.'; return; }

            this.accSaving = true;
            try {
                const fd = new FormData();
                fd.append('account_category_id', this.accForm.category_id);
                fd.append('name', this.accForm.name.trim());
                fd.append('opening_balance', this.accForm.opening_balance || 0);
                fd.append('type', this.accForm.type);

                const res = await fetch('/chart-of-accounts/accounts', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: fd,
                });
                const d = await res.json();

                if (res.ok && d.success) {
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500, timerProgressBar: true });
                    Toast.fire({ icon: 'success', title: d.message });

                    const catIdx = this.categories.findIndex(c => c.id == d.account.category_id);
                    if (catIdx !== -1) {
                        this.categories[catIdx].accounts.push(d.account);
                        this.categories[catIdx].accounts.sort((a, b) => a.name.localeCompare(b.name));
                    }
                    this.closeAccountModal();
                } else {
                    if (d.errors) {
                        Object.keys(d.errors).forEach(k => {
                            this.accErrors[k.replace('account_category_id', 'category_id')] = d.errors[k][0];
                        });
                    } else {
                        this.accErrors.general = d.message || 'Could not save account.';
                    }
                }
            } catch (e) {
                this.accErrors.general = 'Network error. Please try again.';
            }
            this.accSaving = false;
        },

        // ────────────────────────────────
        // TOGGLE ACTIVE
        // ────────────────────────────────
        async toggleAccount(acc) {
            this.togglingId = acc.id;
            try {
                const res = await fetch(`/chart-of-accounts/accounts/${acc.id}/toggle`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: (() => { const f = new FormData(); f.append('_method', 'PATCH'); return f; })(),
                });
                const d = await res.json();
                if (res.ok && d.success) {
                    acc.is_active = d.is_active;
                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    Toast.fire({ icon: 'success', title: d.message });
                }
            } catch (e) {}
            this.togglingId = null;
        },

        // ────────────────────────────────
        // EDIT ACCOUNT
        // ────────────────────────────────
        openEditAccountModal(acc) {
            this.editingAcc = acc;
            this.editAccForm = {
                category_id:     acc.category_id,
                name:            acc.name,
                opening_balance: acc.opening_balance,
                type:            acc.type,
            };
            this.editAccError = '';
            this.editAccModalOpen = true;
        },
        closeEditAccountModal() {
            this.editAccModalOpen = false;
            this.editingAcc = null;
            this.editAccError = '';
        },
        async saveEditAccount() {
            this.editAccError = '';
            if (!this.editAccForm.name.trim()) { this.editAccError = 'Account name is required.'; return; }
            if (!this.editAccForm.type) { this.editAccError = 'Please select a balance type.'; return; }
            this.editAccSaving = true;
            try {
                const fd = new FormData();
                fd.append('_method', 'PATCH');
                fd.append('account_category_id', this.editAccForm.category_id);
                fd.append('name', this.editAccForm.name.trim());
                fd.append('opening_balance', this.editAccForm.opening_balance || 0);
                fd.append('type', this.editAccForm.type);

                const res = await fetch(`/chart-of-accounts/accounts/${this.editingAcc.id}`, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': TOKEN },
                    body: fd,
                });
                const d = await res.json();
                if (res.ok && d.success) {
                    // Update in-memory
                    const acc = this.editingAcc;
                    const oldCatIdx = this.categories.findIndex(c => c.accounts.some(a => a.id === acc.id));
                    const newCatIdx = this.categories.findIndex(c => c.id == this.editAccForm.category_id);

                    if (oldCatIdx !== -1) {
                        const aIdx = this.categories[oldCatIdx].accounts.findIndex(a => a.id === acc.id);
                        if (aIdx !== -1) this.categories[oldCatIdx].accounts.splice(aIdx, 1);
                    }
                    if (newCatIdx !== -1) {
                        this.categories[newCatIdx].accounts.push(d.account);
                        this.categories[newCatIdx].accounts.sort((a, b) => a.name.localeCompare(b.name));
                    }

                    const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                    Toast.fire({ icon: 'success', title: d.message });
                    this.closeEditAccountModal();
                } else {
                    this.editAccError = d.errors?.name?.[0] || d.message || 'Could not update account.';
                }
            } catch (e) {
                this.editAccError = 'Network error. Please try again.';
            }
            this.editAccSaving = false;
        },

        // ────────────────────────────────
        // HELPERS
        // ────────────────────────────────
        money(n) {
            return Number(n || 0).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
    };
}
</script>
@endpush
