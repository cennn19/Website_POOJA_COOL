<?php

namespace App\Console\Commands;

use App\Support\SecurityChecklist;
use Illuminate\Console\Command;

/**
 * Pemeriksaan konfigurasi keamanan sebelum/sesudah deploy.
 * Jalankan di server: php artisan app:cek-keamanan
 * (Hosting tanpa terminal: buka menu admin "Cek Keamanan".)
 * Keluar dengan kode 1 bila ada pemeriksaan WAJIB yang gagal.
 */
class CekKeamanan extends Command
{
    protected $signature = 'app:cek-keamanan';

    protected $description = 'Periksa konfigurasi keamanan server (APP_DEBUG, HTTPS, password admin, dll.)';

    public function handle(SecurityChecklist $checklist): int
    {
        $this->line('Pemeriksaan keamanan konfigurasi');
        $this->newLine();

        $checks = $checklist->run();

        foreach ($checks as $check) {
            $label = match ($check['status']) {
                SecurityChecklist::PASS => '<fg=green>LOLOS</>',
                SecurityChecklist::FAIL => '<fg=red>GAGAL</>',
                default => '<fg=yellow>SARAN</>',
            };

            $this->line("  {$label}  {$check['name']}");

            if ($check['advice'] !== '') {
                $this->line("         → {$check['advice']}");
            }
        }

        $this->newLine();
        $failures = SecurityChecklist::failures($checks);

        if ($failures > 0) {
            $this->error("{$failures} pemeriksaan wajib GAGAL. Perbaiki sebelum website dibuka untuk umum.");

            return self::FAILURE;
        }

        $this->info('Semua pemeriksaan wajib lolos.');

        return self::SUCCESS;
    }
}
