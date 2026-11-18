<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SecurityChecklist;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman "Cek Keamanan" untuk hosting tanpa terminal (InfinityFree):
 * isi sama dengan `php artisan app:cek-keamanan`, plus diagnosa IP/proxy.
 */
class SecurityCheckController extends Controller
{
    public function __invoke(Request $request, SecurityChecklist $checklist): View
    {
        $checks = $checklist->run();

        $diagnostics = [
            'IP pengunjung menurut aplikasi' => $request->ip(),
            'REMOTE_ADDR (koneksi langsung)' => $request->server('REMOTE_ADDR'),
            'Header X-Forwarded-For' => $request->header('X-Forwarded-For') ?: '(tidak ada)',
            'Diakses lewat HTTPS' => $request->secure() ? 'ya' : 'tidak',
            'TRUSTED_PROXIES' => env('TRUSTED_PROXIES') ?: '(kosong)',
            'Versi PHP' => PHP_VERSION,
            'Server' => $request->server('SERVER_SOFTWARE') ?: '-',
        ];

        // Ada proxy di depan server, tetapi belum dipercaya: semua pengunjung terlihat
        // ber-IP sama sehingga batas akses (throttle) berlaku bersama untuk semua orang.
        $proxyWarning = $request->header('X-Forwarded-For') && ! env('TRUSTED_PROXIES');

        return view('admin.security.index', [
            'checks' => $checks,
            'failures' => SecurityChecklist::failures($checks),
            'diagnostics' => $diagnostics,
            'proxyWarning' => $proxyWarning,
        ]);
    }
}
