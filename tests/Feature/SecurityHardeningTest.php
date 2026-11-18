<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Finder\Finder;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Header keamanan
    // ---------------------------------------------------------------

    public function test_pages_send_security_headers(): void
    {
        $response = $this->get(route('home'))->assertOk();

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');

        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertMatchesRegularExpression("/script-src 'self' 'unsafe-eval'(;|$)/", $csp, 'Skrip inline tidak boleh diizinkan.');
    }

    public function test_not_found_pages_also_get_security_headers(): void
    {
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Content-Security-Policy');
    }

    public function test_hsts_only_over_https(): void
    {
        $this->get(route('home'))->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_pages_with_personal_data_are_not_cached(): void
    {
        $order = Order::factory()->create();

        foreach ([route('order.status', $order->code), route('payment.show', $order->code), route('admin.login')] as $url) {
            $cache = $this->get($url)->assertOk()->headers->get('Cache-Control');
            $this->assertStringContainsString('no-store', $cache, $url);
        }

        $this->actingAs(User::factory()->create())
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_views_have_no_inline_event_handlers_blocked_by_csp(): void
    {
        $offenders = [];

        foreach (Finder::create()->files()->in(resource_path('views'))->name('*.blade.php') as $file) {
            if (preg_match('/\son(click|submit|change|input|load|error|keyup|keydown|focus|blur)\s*=/i', $file->getContents())) {
                $offenders[] = $file->getRelativePathname();
            }
        }

        $this->assertSame([], $offenders, 'Gunakan @click/@submit (Alpine), bukan onclick/onsubmit.');
    }

    // ---------------------------------------------------------------
    // File bukti bayar
    // ---------------------------------------------------------------

    public function test_payment_proof_is_served_sandboxed_and_only_to_admins(): void
    {
        Storage::fake('private');
        $path = UploadedFile::fake()->image('bukti.jpg', 20, 20)->store('payments', 'private');
        $order = Order::factory()->create();
        $payment = $order->payments()->create(['proof_path' => $path, 'status' => 'pending']);

        $this->get(route('admin.payments.proof', $payment))->assertRedirect(route('admin.login'));

        $response = $this->actingAs(User::factory()->create())
            ->get(route('admin.payments.proof', $payment))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $this->assertStringContainsString('sandbox', $response->headers->get('Content-Security-Policy'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_non_image_proof_file_is_never_served(): void
    {
        Storage::fake('private');
        Storage::disk('private')->put('payments/jahat.jpg', '<html><script>alert(1)</script></html>');
        $payment = Order::factory()->create()->payments()->create(['proof_path' => 'payments/jahat.jpg', 'status' => 'pending']);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.payments.proof', $payment))
            ->assertNotFound();
    }

    public function test_unused_storage_serving_route_is_disabled(): void
    {
        $this->assertFalse(Route::has('storage.local'));
        $this->assertFalse(Route::has('storage.local.upload'));
    }

    // ---------------------------------------------------------------
    // Seeder admin
    // ---------------------------------------------------------------

    private function seedAdmin(array $env): void
    {
        foreach ($env as $key => $value) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $_SERVER[$key] = $value;
        }

        try {
            // Dipanggil langsung: `db:seed` di mode produksi meminta konfirmasi interaktif.
            $this->app->make(UserSeeder::class)->run();
        } finally {
            foreach (array_keys($env) as $key) {
                putenv($key);
                unset($_ENV[$key], $_SERVER[$key]);
            }
        }
    }

    public function test_production_seeder_refuses_missing_or_weak_admin_password(): void
    {
        $this->app['env'] = 'production';

        foreach (['', 'password123', 'pendek123'] as $password) {
            try {
                $this->seedAdmin(['ADMIN_EMAIL' => 'admin@pooja.test', 'ADMIN_PASSWORD' => $password]);
                $this->fail("Seeder seharusnya menolak password: '{$password}'");
            } catch (RuntimeException $e) {
                $this->assertStringContainsString('ADMIN_PASSWORD', $e->getMessage());
            }
        }

        $this->assertSame(0, User::count());

        $this->seedAdmin(['ADMIN_EMAIL' => 'admin@pooja.test', 'ADMIN_PASSWORD' => 'Rahasia-Kuat-2026']);
        $this->assertTrue(Hash::check('Rahasia-Kuat-2026', User::sole()->password));
    }

    public function test_reseeding_never_overwrites_a_changed_admin_password(): void
    {
        $this->seedAdmin(['ADMIN_EMAIL' => 'admin@pooja.test', 'ADMIN_PASSWORD' => 'Awal-Password-1']);
        User::sole()->update(['password' => 'Diganti-Admin-2']);

        $this->seedAdmin(['ADMIN_EMAIL' => 'admin@pooja.test', 'ADMIN_PASSWORD' => 'Awal-Password-1']);

        $this->assertTrue(Hash::check('Diganti-Admin-2', User::sole()->password));
    }

    // ---------------------------------------------------------------
    // Ganti password admin
    // ---------------------------------------------------------------

    public function test_guest_cannot_open_change_password_page(): void
    {
        $this->get(route('admin.password.edit'))->assertRedirect(route('admin.login'));
    }

    public function test_change_password_requires_correct_current_password_and_strong_new_one(): void
    {
        $admin = User::factory()->create(['password' => 'Lama-Password-1']);
        $this->actingAs($admin)->get(route('admin.password.edit'))->assertOk()->assertSee('Ganti Password');

        $cases = [
            [['current_password' => 'salah', 'password' => 'Baru-Password-2', 'password_confirmation' => 'Baru-Password-2'], 'current_password'],
            [['current_password' => 'Lama-Password-1', 'password' => 'pendek1', 'password_confirmation' => 'pendek1'], 'password'],
            [['current_password' => 'Lama-Password-1', 'password' => 'hanyahurufsaja', 'password_confirmation' => 'hanyahurufsaja'], 'password'],
            [['current_password' => 'Lama-Password-1', 'password' => 'Baru-Password-2', 'password_confirmation' => 'beda'], 'password'],
            [['current_password' => 'Lama-Password-1', 'password' => 'Lama-Password-1', 'password_confirmation' => 'Lama-Password-1'], 'password'],
        ];

        foreach ($cases as [$data, $field]) {
            $this->actingAs($admin)->put(route('admin.password.update'), $data)->assertSessionHasErrors($field);
        }

        $this->assertTrue(Hash::check('Lama-Password-1', $admin->fresh()->password));
    }

    public function test_changing_password_works_and_logs_out_other_devices(): void
    {
        config()->set('session.driver', 'database');
        $admin = User::factory()->create(['password' => 'Lama-Password-1', 'remember_token' => 'token-lama']);

        // Sesi di perangkat lain milik admin ini, dan sesi milik user lain.
        $other = User::factory()->create();
        DB::table('sessions')->insert([
            ['id' => 'sesi-hp-admin', 'user_id' => $admin->id, 'ip_address' => '1.1.1.1', 'user_agent' => 'HP', 'payload' => '', 'last_activity' => time()],
            ['id' => 'sesi-user-lain', 'user_id' => $other->id, 'ip_address' => '2.2.2.2', 'user_agent' => 'PC', 'payload' => '', 'last_activity' => time()],
        ]);

        $this->actingAs($admin)
            ->put(route('admin.password.update'), [
                'current_password' => 'Lama-Password-1',
                'password' => 'Baru-Password-2',
                'password_confirmation' => 'Baru-Password-2',
            ])
            ->assertRedirect(route('admin.password.edit'))
            ->assertSessionHas('success');

        $admin->refresh();
        $this->assertTrue(Hash::check('Baru-Password-2', $admin->password));
        $this->assertNotSame('token-lama', $admin->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'sesi-hp-admin']);
        $this->assertDatabaseHas('sessions', ['id' => 'sesi-user-lain']);
    }

    // ---------------------------------------------------------------
    // Pemeriksaan konfigurasi server
    // ---------------------------------------------------------------

    public function test_security_check_page_is_admin_only_and_lists_checks(): void
    {
        $this->get(route('admin.security.index'))->assertRedirect(route('admin.login'));

        config()->set('app.debug', true);

        $this->actingAs(User::factory()->create())
            ->get(route('admin.security.index'))
            ->assertOk()
            ->assertSee('APP_DEBUG = false')
            ->assertSee('pemeriksaan wajib gagal')
            ->assertSee('Versi PHP');
    }

    public function test_security_check_page_warns_about_untrusted_proxy(): void
    {
        $this->actingAs(User::factory()->create())
            ->withHeader('X-Forwarded-For', '203.0.113.9')
            ->get(route('admin.security.index'))
            ->assertOk()
            ->assertSee('TRUSTED_PROXIES=*');
    }

    public function test_security_check_command_fails_on_unsafe_config(): void
    {
        config()->set('app.debug', true);

        $this->artisan('app:cek-keamanan')
            ->expectsOutputToContain('GAGAL')
            ->assertFailed();
    }

    public function test_security_check_command_passes_on_safe_production_config(): void
    {
        $this->app['env'] = 'production';
        config()->set('app.debug', false);
        config()->set('app.url', 'https://poojacool.example');
        config()->set('session.secure', true);
        User::factory()->create(['password' => 'Kuat-Sekali-2026']);

        $manifest = public_path('build/manifest.json');
        $this->assertFileExists($manifest, 'Jalankan npm run build sebelum tes ini.');

        $this->artisan('app:cek-keamanan')
            ->expectsOutputToContain('Semua pemeriksaan wajib lolos')
            ->assertSuccessful();
    }
}
