<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Order Pelanggan
Route::get('/order', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('order.store');

// Pembayaran Pelanggan
Route::get('/pembayaran/{code}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/pembayaran/{code}', [PaymentController::class, 'uploadProof'])
    ->middleware('throttle:10,1')
    ->name('payment.upload');

// Cek Status Order Pelanggan
Route::get('/status/{code?}', [OrderController::class, 'status'])->name('order.status');

// Autentikasi Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('login.store');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    // Panel Admin (Dilindungi Auth)
    Route::middleware('auth')->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');

        // Stream file bukti transfer private
        Route::get('/payments/{payment}/proof', [AdminOrderController::class, 'streamProof'])->name('payments.proof');
    });
});
