<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login admin.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.orders.index');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi login admin.
     */
    public function store(AdminLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => ['Kombinasi email dan kata sandi tidak cocok.'],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.orders.index'));
    }

    /**
     * Logout admin.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
