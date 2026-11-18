<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PasswordController as AdminPasswordController;
use App\Http\Controllers\Admin\SecurityCheckController as AdminSecurityCheckController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Halaman Publik
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Catatan throttle: parameter ketiga (prefix) WAJIB diisi. Tanpa prefix, semua throttle
// berbagi satu penghitung per IP, sehingga membuka halaman status/pembayaran beberapa kali
// ikut menghabiskan jatah membuat pesanan dan unggah bukti (pelanggan kena 429).

// Halaman yang bergantung pada status pesanan: batalkan dulu pesanan kedaluwarsa
// (pengganti cron untuk hosting tanpa cron, lihat ExpireUnpaidOrders).
Route::middleware('expire.orders')->group(function () {
    // Order Pelanggan
    Route::get('/order', [OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [OrderController::class, 'store'])
        ->middleware('throttle:10,1,buat-pesanan')
        ->name('order.store');

    // Pembayaran Pelanggan
    // Halaman pembayaran & cek status dibatasi agar kode pesanan tidak bisa ditebak beruntun.
    Route::get('/pembayaran/{code}', [PaymentController::class, 'show'])
        ->middleware('throttle:30,1,lihat-pesanan')
        ->name('payment.show');
    Route::post('/pembayaran/{code}', [PaymentController::class, 'uploadProof'])
        ->middleware('throttle:10,1,unggah-bukti')
        ->name('payment.upload');

    // Cek Status Order Pelanggan
    Route::get('/status/{code?}', [OrderController::class, 'status'])
        ->middleware('throttle:30,1,lihat-pesanan')
        ->name('order.status');
});

// Autentikasi Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'store'])
        ->middleware('throttle:5,1,login-admin')
        ->name('login.store');
    Route::post('/logout', [AdminAuthController::class, 'destroy'])
        ->middleware('auth')
        ->name('logout');

    // Panel Admin (Dilindungi Auth)
    Route::middleware(['auth', 'expire.orders'])->group(function () {
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/approve', [AdminOrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [AdminOrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/reschedule', [AdminOrderController::class, 'reschedule'])->name('orders.reschedule');

        // Cek keamanan server (pengganti `php artisan app:cek-keamanan` di hosting tanpa terminal)
        Route::get('/keamanan', AdminSecurityCheckController::class)->name('security.index');

        // Ganti password admin
        Route::get('/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
        Route::put('/password', [AdminPasswordController::class, 'update'])
            ->middleware('throttle:5,1,ganti-password')
            ->name('password.update');

        // Layanan & Harga
        Route::get('/services', [AdminServiceController::class, 'index'])->name('services.index');
        Route::put('/services', [AdminServiceController::class, 'bulkUpdate'])->name('services.bulk-update');
        Route::put('/services/{service}', [AdminServiceController::class, 'update'])->name('services.update');
        Route::post('/kategori-layanan/{kategori}/services', [AdminServiceController::class, 'store'])->name('services.store');
        Route::put('/kategori-layanan/{kategori}', [AdminServiceController::class, 'updateKategori'])->name('kategori.update');

        // Stream file bukti transfer private
        Route::get('/payments/{payment}/proof', [AdminOrderController::class, 'streamProof'])->name('payments.proof');
    });
});
