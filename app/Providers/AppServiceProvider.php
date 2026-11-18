<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Jika situs dipasang dengan HTTPS, semua URL yang dibuat Laravel (form, redirect,
        // aset) ikut HTTPS, termasuk saat hosting meneruskan request lewat proxy HTTP.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        // Aturan password admin: minimal 10 karakter, huruf dan angka.
        Password::defaults(fn () => Password::min(10)->letters()->numbers());
    }
}
