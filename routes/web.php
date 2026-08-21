<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
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

    return view('dashboard', compact('totalCustomers', 'totalServices', 'newOrders'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/members', [CustomerController::class, 'getMembers'])->name('customers.members');
    Route::post('api/customers', [CustomerController::class, 'apiStore'])->name('api.customers.store');
    Route::resource('services', ServiceController::class);

    Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::get('api/orders/services', [OrderController::class, 'apiServices'])->name('api.orders.services');
    Route::get('api/orders/previous-measurements', [OrderController::class, 'apiPreviousMeasurements'])->name('api.orders.prevMeasurements');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
