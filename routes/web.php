<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $totalCustomers = \App\Models\Customer::count();
    $totalServices = \App\Models\Service::where('is_active', true)->count();
    $newOrders = \App\Models\Order::where('status', 'pending')->count();

    $todayReceived = (float) \App\Models\Payment::whereDate('created_at', today())->sum('amount');
    $totalOutstanding = (float) \App\Models\Order::whereIn('payment_status', ['unpaid', 'partial'])
        ->sum(\Illuminate\Support\Facades\DB::raw('price * quantity - paid_amount'));
    $recentPayments = \App\Models\Payment::with(['order.customer:id,name'])
        ->latest('payments.id')
        ->limit(5)
        ->get()
        ->map(fn ($p) => [
            'order_no' => str_pad($p->order_id, 4, '0', STR_PAD_LEFT),
            'customer' => $p->order?->customer?->name ?? '—',
            'amount'   => (float) $p->amount,
            'method'   => str_replace('_', ' ', $p->method),
            'date'     => $p->created_at->diffForHumans(),
        ]);

    return view('dashboard', compact('totalCustomers', 'totalServices', 'newOrders', 'todayReceived', 'totalOutstanding', 'recentPayments'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/members', [CustomerController::class, 'getMembers'])->name('customers.members');
    Route::resource('services', ServiceController::class);

    // Measurements (decoupled from Orders)
    Route::get('measurements/create', [MeasurementController::class, 'create'])->name('measurements.create');
    Route::post('measurements', [MeasurementController::class, 'store'])->name('measurements.store');
    Route::get('measurements', [MeasurementController::class, 'index'])->name('measurements.index');
    Route::get('measurements/{measurement}', [MeasurementController::class, 'show'])->name('measurements.show');
    Route::get('measurements/{measurement}/edit', [MeasurementController::class, 'edit'])->name('measurements.edit');
    Route::put('measurements/{measurement}', [MeasurementController::class, 'update'])->name('measurements.update');
    Route::delete('measurements/{measurement}', [MeasurementController::class, 'destroy'])->name('measurements.destroy');

    // Orders
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('api/payments/search', [PaymentController::class, 'apiSearch'])->name('api.payments.search');
    Route::get('api/payments/orders/{order}/payments', [PaymentController::class, 'apiPayments'])->name('api.payments.order');
    Route::get('api/payments/recent', [PaymentController::class, 'apiRecentPayments'])->name('api.payments.recent');

    // Chart of Accounts
    Route::get('chart-of-accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('chart-of-accounts/categories', [AccountController::class, 'storeCategory'])->name('accounts.categories.store');
    Route::patch('chart-of-accounts/categories/{category}', [AccountController::class, 'updateCategory'])->name('accounts.categories.update');
    Route::post('chart-of-accounts/accounts', [AccountController::class, 'storeAccount'])->name('accounts.store');
    Route::patch('chart-of-accounts/accounts/{account}/toggle', [AccountController::class, 'toggleActive'])->name('accounts.toggle');
    Route::get('chart-of-accounts/{account}/ledger', [AccountController::class, 'ledger'])->name('accounts.ledger');

    // Shared Wizard APIs (used by measurements create form)
    Route::get('api/orders/search-customers', [OrderController::class, 'apiSearchCustomers'])->name('api.orders.searchCustomers');
    Route::get('api/orders/search-all', [OrderController::class, 'apiSearchAll'])->name('api.orders.searchAll');
    Route::post('api/orders/quick-customer', [OrderController::class, 'apiQuickCustomer'])->name('api.orders.quickCustomer');
    Route::post('api/orders/quick-member', [OrderController::class, 'apiQuickMember'])->name('api.orders.quickMember');
    Route::get('api/orders/services', [OrderController::class, 'apiServices'])->name('api.orders.services');
    Route::get('api/orders/previous-measurements', [OrderController::class, 'apiPreviousMeasurements'])->name('api.orders.prevMeasurements');
    Route::get('api/orders/customer-ledger/{customer}', [OrderController::class, 'apiCustomerLedger'])->name('api.orders.customerLedger');
    Route::get('api/orders/customer/{customer}', [OrderController::class, 'apiCustomer'])->name('api.orders.customer');
    Route::get('api/orders/member-services', [OrderController::class, 'apiMemberServices'])->name('api.orders.memberServices');
    Route::get('api/measurements/customer/{customer}', [MeasurementController::class, 'apiCustomerMeasurements'])->name('api.measurements.customer');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
