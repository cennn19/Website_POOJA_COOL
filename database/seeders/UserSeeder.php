<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    /** Panjang minimal password admin di server produksi. */
    public const MIN_PRODUCTION_PASSWORD = 12;

    /** Password bawaan khusus development lokal. Ditolak di produksi. */
    public const LOCAL_DEFAULT_PASSWORD = 'password123';

    /**
     * Buat akun admin awal dari .env (ADMIN_EMAIL, ADMIN_NAME, ADMIN_PASSWORD).
     *
     * - Di produksi ADMIN_PASSWORD wajib diisi dan minimal 12 karakter.
     * - Akun yang sudah ada TIDAK diubah, sehingga menjalankan seeder ulang saat deploy
     *   tidak menimpa password yang sudah diganti admin lewat panel.
     */
    public function run(): void
    {
        $production = app()->environment('production');
        $email = env('ADMIN_EMAIL') ?: ($production ? null : 'admin@poojacool.test');
        $name = env('ADMIN_NAME') ?: 'Admin Pooja Cool';
        $password = (string) env('ADMIN_PASSWORD', '');

        if ($production) {
            if (! $email) {
                throw new RuntimeException('ADMIN_EMAIL wajib diisi di .env server sebelum menjalankan seeder.');
            }

            if (mb_strlen($password) < self::MIN_PRODUCTION_PASSWORD || $password === self::LOCAL_DEFAULT_PASSWORD) {
                throw new RuntimeException('ADMIN_PASSWORD wajib diisi di .env server, minimal '.self::MIN_PRODUCTION_PASSWORD.' karakter.');
            }
        } elseif ($password === '') {
            $password = self::LOCAL_DEFAULT_PASSWORD;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password, // di-hash otomatis oleh cast "hashed" pada model User
                'email_verified_at' => now(),
            ],
        );

        if (! $user->wasRecentlyCreated) {
            $this->command?->info("Akun admin {$email} sudah ada, password tidak diubah.");
        }
    }
}
