<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Hash;

/**
 * Daftar pemeriksaan konfigurasi keamanan server.
 * Dipakai oleh perintah `php artisan app:cek-keamanan` dan halaman admin "Cek Keamanan"
 * (untuk hosting tanpa terminal seperti InfinityFree).
 */
class SecurityChecklist
{
    public const PASS = 'lolos';

    public const FAIL = 'gagal';

    public const WARN = 'saran';

    /** Versi PHP minimal (composer.json). */
    public const MIN_PHP = '8.3.0';

    /**
     * @return array<int, array{status: string, name: string, advice: string}>
     */
    public function run(): array
    {
        $checks = [];

        $add = function (bool $ok, string $name, string $advice, bool $required = true) use (&$checks) {
            $checks[] = [
                'status' => $ok ? self::PASS : ($required ? self::FAIL : self::WARN),
                'name' => $name,
                'advice' => $ok ? '' : $advice,
            ];
        };

        $add(version_compare(PHP_VERSION, self::MIN_PHP, '>='), 'Versi PHP '.PHP_VERSION.' (minimal '.self::MIN_PHP.')', 'Ganti versi PHP hosting ke 8.3 atau lebih baru.');
        $add(app()->environment('production'), 'APP_ENV = production', 'Set APP_ENV=production di .env server.');
        $add(! config('app.debug'), 'APP_DEBUG = false', 'Set APP_DEBUG=false. Jika true, halaman error menampilkan password database ke publik.');
        $add(filled(config('app.key')), 'APP_KEY terisi', 'Isi APP_KEY (dibuat otomatis oleh skrip build deploy).');
        $add(str_starts_with((string) config('app.url'), 'https://'), 'APP_URL memakai https://', 'Aktifkan SSL lalu ubah APP_URL menjadi https://domain-anda');
        $add((bool) config('session.secure'), 'Cookie sesi hanya lewat HTTPS', 'Otomatis aktif jika APP_URL https, atau set SESSION_SECURE_COOKIE=true.');
        $add(! file_exists(public_path('hot')), 'Tidak ada file "hot" (sisa npm run dev)', 'Hapus file hot di folder publik lalu upload hasil npm run build.');
        $add(file_exists(public_path('build/manifest.json')), 'Aset produksi sudah di-build', 'Upload folder build hasil npm run build.');
        $add(! file_exists(public_path('.env')), 'Tidak ada file .env di folder publik', 'Pindahkan .env ke folder aplikasi (bukan folder publik).');
        $add(is_writable(storage_path('framework')) && is_writable(storage_path('logs')), 'Folder storage bisa ditulis', 'Pastikan folder storage (dan isinya) bisa ditulis oleh PHP.');

        $weak = User::all()->filter(fn (User $u) => Hash::check(UserSeeder::LOCAL_DEFAULT_PASSWORD, $u->password)
            || Hash::check('password', $u->password));
        $add($weak->isEmpty(), 'Tidak ada admin dengan password bawaan', 'Ganti password akun: '.$weak->pluck('email')->join(', ').' (menu Ganti Password).');

        $level = config('logging.channels.'.config('logging.default').'.level', 'debug');
        $add($level !== 'debug', 'LOG_LEVEL bukan debug', 'Disarankan LOG_LEVEL=error di server.', required: false);

        return $checks;
    }

    /**
     * @param  array<int, array{status: string}>  $checks
     */
    public static function failures(array $checks): int
    {
        return count(array_filter($checks, fn ($c) => $c['status'] === self::FAIL));
    }
}
