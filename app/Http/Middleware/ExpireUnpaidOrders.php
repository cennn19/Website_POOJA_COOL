<?php

namespace App\Http\Middleware;

use App\Services\UnpaidOrderExpirer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Pengganti cron untuk hosting yang tidak menyediakan cron: sebelum halaman pesanan,
 * pembayaran, cek status, atau admin diproses, batalkan dulu pesanan yang kedaluwarsa
 * agar kuota sesi dan kode unik yang ditahannya kembali tersedia.
 */
class ExpireUnpaidOrders
{
    public function __construct(private UnpaidOrderExpirer $expirer) {}

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->expirer->expireIfDue();
        } catch (Throwable $e) {
            // Kegagalan pembersihan tidak boleh membuat halaman pelanggan error.
            report($e);
        }

        return $next($request);
    }
}
