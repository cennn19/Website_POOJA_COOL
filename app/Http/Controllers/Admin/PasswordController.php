<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function edit(): View
    {
        return view('admin.password.edit');
    }

    /**
     * Ganti password admin, lalu keluarkan semua sesi lain (perangkat lain yang mungkin
     * masih login dengan password lama, termasuk cookie "ingat saya").
     */
    public function update(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => $request->validated('password'), // di-hash oleh cast "hashed"
            'remember_token' => Str::random(60),             // batalkan cookie "ingat saya" lama
        ])->save();

        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getAuthIdentifier())
                ->where('id', '!=', $request->session()->getId())
                ->delete();
        }

        $request->session()->regenerate(true);

        return redirect()
            ->route('admin.password.edit')
            ->with('success', 'Password berhasil diganti. Perangkat lain yang masih login telah dikeluarkan.');
    }
}
