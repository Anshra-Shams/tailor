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

    <!-- Stats Cards Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5">
        <!-- Total Customers -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Customers</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ $totalCustomers }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold inline-flex items-center gap-1">
                    <span>View all customers</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- Received Today -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Received Today</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">Rs. {{ number_format($todayReceived) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-100 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('payments.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-semibold inline-flex items-center gap-1">
                    <span>Receipts voucher</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- Received Monthly -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Received Monthly</p>
                    <p class="text-3xl font-bold text-teal-600 mt-1">Rs. {{ number_format($monthlyReceived) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center group-hover:bg-teal-100 transition-colors">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('reports.index', ['preset' => 'this_month']) }}" class="text-xs text-teal-600 hover:text-teal-700 font-semibold inline-flex items-center gap-1">
                    <span>This month report</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Orders</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ $newOrders }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="text-xs text-amber-600 hover:text-amber-700 font-semibold inline-flex items-center gap-1">
                    <span>Manage orders</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>

        <!-- Total Outstanding -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:shadow-lg hover:shadow-slate-200/50 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Outstanding Balance</p>
                    <p class="text-3xl font-bold text-rose-600 mt-1">Rs. {{ number_format($totalOutstanding) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100">
                <a href="{{ route('payments.index') }}" class="text-xs text-rose-600 hover:text-rose-700 font-semibold inline-flex items-center gap-1">
                    <span>Collect pending due</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= ACCOUNT BALANCES CARDS SECTION ================= -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-5 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                    <span>Accounts & Current Balances</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Real-time balances across all your active cash and bank accounts</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700">
                    <span>Total Balances:</span>
                    <strong class="text-sm ml-1 {{ $totalAccountBalance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        Rs. {{ number_format($totalAccountBalance) }}
                    </strong>
                </div>
                @can('view-accounts')
                <a href="{{ route('accounts.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-xl transition-colors">
                    <span>Manage Accounts</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
                @endcan
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-5">
            @forelse($accounts as $acc)
            @php
                $isNegative = (float) $acc->current_balance < 0;
                $catName = strtolower($acc->category?->name ?? '');
                $isBank = str_contains($catName, 'bank') || str_contains(strtolower($acc->name), 'bank') || str_contains(strtolower($acc->name), 'hbl');
            @endphp
            <div class="relative p-5 rounded-2xl border transition-all duration-200 hover:shadow-md group {{ $isNegative ? 'bg-gradient-to-br from-rose-50/40 to-white border-rose-200' : 'bg-gradient-to-br from-slate-50/60 to-white border-slate-200' }}">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $isBank ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                            @if($isBank)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">
                                {{ $acc->name }}
                            </h4>
                            <span class="inline-block text-[11px] font-medium text-slate-400 capitalize">
                                {{ $acc->category?->name ?? 'General Account' }}
                            </span>
                        </div>
                    </div>

                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $acc->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        {{ $acc->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100/80 flex items-end justify-between">
                    <div>
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">Current Balance</span>
                        <div class="text-2xl font-extrabold mt-0.5 {{ $isNegative ? 'text-rose-600' : 'text-slate-800' }}">
                            Rs. {{ number_format($acc->current_balance) }}
                        </div>
                    </div>
                    @can('view-accounts')
                    <a href="{{ route('accounts.ledger', $acc->id) }}"
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 inline-flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                        <span>Ledger</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                    </a>
                    @endcan
                </div>
            </div>
            @empty
            <div class="col-span-full py-8 text-center text-slate-400">
                <p class="text-sm">No accounts found. Create accounts in Chart of Accounts.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- ================= RECENT ACTIVITY: RECENT ORDERS & PAYMENTS ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Orders -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Recent Orders</h3>
                    <p class="text-xs text-slate-400">Latest orders placed</p>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 inline-flex items-center gap-1">
                    <span>View all</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="px-5 py-3 text-left">Order #</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-left">Service</th>
                            <th class="px-4 py-3 text-right">Amount</th>
                            <th class="px-5 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-5 py-3 font-bold text-indigo-600">
                                <a href="{{ route('orders.invoice', $order->id) }}" class="hover:underline">
                                    #{{ str_pad($order->order_number ?? $order->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-800 text-xs">
                                {{ $order->customer?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs">
                                {{ $order->service?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800 text-xs">
                                Rs. {{ number_format($order->totalAmount()) }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full
                                    {{ in_array($order->status, ['completed', 'delivered']) ? 'bg-emerald-100 text-emerald-800' : ($order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-400 text-xs">
                                No recent orders found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments Received -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-800 text-base">Recent Payments Received</h3>
                    <p class="text-xs text-slate-400">Latest receipts and vouchers collected</p>
                </div>
                <a href="{{ route('payments.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 inline-flex items-center gap-1">
                    <span>View all</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentPayments as $payment)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50/70 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                            Rs
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800 text-xs">{{ $payment['customer'] }}</span>
                                <span class="text-[11px] font-semibold text-indigo-600">#{{ $payment['order_no'] }}</span>
                            </div>
                            <span class="text-[11px] text-slate-400">{{ $payment['date'] }} • {{ ucfirst($payment['method']) }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-bold text-emerald-600">+Rs. {{ number_format($payment['amount']) }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-slate-400 text-xs">
                    No recent payments recorded.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
