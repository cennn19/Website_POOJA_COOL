<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan untuk semua halaman web.
 *
 * - CSP: skrip hanya dari domain sendiri (tanpa skrip inline). 'unsafe-eval' dibutuhkan
 *   Alpine.js untuk mengevaluasi atribut x-data / @click.
 * - Anti-clickjacking (frame-ancestors + X-Frame-Options), nosniff, Referrer-Policy
 *   (kode pesanan di URL tidak bocor ke situs lain, misal wa.me), Permissions-Policy.
 * - HSTS hanya saat diakses lewat HTTPS.
 * - Halaman berisi data pribadi (admin, pembayaran, cek status) tidak boleh disimpan cache.
 */
class SecurityHeaders
{
    /** Halaman yang memuat data pesanan/pribadi: jangan disimpan di cache browser/proxy. */
    private const PRIVATE_ROUTES = ['admin.*', 'payment.*', 'order.status', 'order.create', 'order.store'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $headers = $response->headers;

        // Respons yang sudah punya CSP sendiri (misal file bukti bayar) tidak ditimpa.
        if (! $headers->has('Content-Security-Policy')) {
            $headers->set('Content-Security-Policy', $this->contentSecurityPolicy($request));
        }

        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=()');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        if ($request->secure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if ($request->route()?->named(...self::PRIVATE_ROUTES)) {
            $headers->set('Cache-Control', 'no-store, private');
            $headers->set('Pragma', 'no-cache');
        }

        return $response;
    }

    private function contentSecurityPolicy(Request $request): string
    {
        // Saat `npm run dev`, aset disajikan server Vite (beda port): izinkan khusus lokal.
        $dev = '';
        if (Vite::isRunningHot()) {
            $hot = rtrim((string) @file_get_contents(public_path('hot')));
            $ws = preg_replace('#^http#', 'ws', $hot);
            $dev = ' '.$hot.' '.$ws;
        }

        $directives = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-eval'".$dev,
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com".$dev,
            "font-src 'self' https://fonts.gstatic.com data:",
            // images.unsplash.com: foto ilustrasi di beranda.
            "img-src 'self' data: blob: https://images.unsplash.com",
            "connect-src 'self'".$dev,
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        if ($request->secure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}
