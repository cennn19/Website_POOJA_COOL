<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Batalkan otomatis pesanan yang tidak dibayar dalam batas waktu (config/booking.php).
// Cron bersifat opsional: tanpa cron, pembatalan tetap berjalan saat ada pengunjung
// (middleware ExpireUnpaidOrders). Jika hosting mendukung, cron membuat status
// berubah tepat waktu walau website sepi: php artisan schedule:run (tiap menit).
Schedule::command('orders:expire')->hourly()->withoutOverlapping();
