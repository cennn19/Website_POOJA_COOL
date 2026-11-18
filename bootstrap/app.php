<?php

use App\Http\Middleware\ExpireUnpaidOrders;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->alias([
            'expire.orders' => ExpireUnpaidOrders::class,
        ]);
        // Hosting yang meneruskan request lewat proxy: isi TRUSTED_PROXIES (misal "*") agar IP
        // pengunjung dan HTTPS terbaca benar. Kosong = tidak percaya header X-Forwarded-*
        // (mencegah pemalsuan IP untuk mengakali batas akses). Lihat menu admin "Cek Keamanan".
        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        // Global (bukan hanya grup web) agar halaman 404/error juga mendapat header keamanan.
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
