<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CancelOrderRequest;
use App\Http\Requests\RejectOrderRequest;
use App\Http\Requests\RescheduleOrderRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\BookingSchedule;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /** Tipe file bukti bayar yang boleh ditampilkan (sama dengan aturan unggah). */
    private const PROOF_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

    /**
     * Tampilkan daftar pesanan dengan filter status, pencarian, dan paginasi.
     */
    public function index(Request $request): View
    {
        $query = Order::query()->with(['items', 'latestPayment']);

        $statusEnum = $request->filled('status') ? OrderStatus::tryFrom($request->status) : null;

        if ($statusEnum) {
            $query->where('status', $statusEnum);
        } elseif (! $request->boolean('tampilkan_batal')) {
            // Default: pesanan dibatalkan disembunyikan dari daftar.
            $query->where('status', '!=', OrderStatus::DIBATALKAN);
        }

        if ($request->filled('q')) {
            $search = '%'.trim($request->q).'%';
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('code', 'like', $search)
                    ->orWhere('name', 'like', $search)
                    ->orWhere('phone', 'like', $search);
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total' => Order::where('status', '!=', OrderStatus::DIBATALKAN)->count(),
            'menunggu_pembayaran' => Order::where('status', OrderStatus::MENUNGGU_PEMBAYARAN)->count(),
            'menunggu_verifikasi' => Order::where('status', OrderStatus::MENUNGGU_VERIFIKASI)->count(),
            'disetujui' => Order::where('status', OrderStatus::DISETUJUI)->count(),
            'ditolak' => Order::where('status', OrderStatus::DITOLAK)->count(),
            'selesai' => Order::where('status', OrderStatus::SELESAI)->count(),
            'dibatalkan' => Order::where('status', OrderStatus::DIBATALKAN)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    /**
     * Tampilkan detail pesanan untuk verifikasi admin.
     */
    public function show(Order $order, BookingSchedule $schedule): View
    {
        $order->load(['items.service', 'payments.verifier', 'logs.user']);
        $sessions = $schedule->sessions();

        return view('admin.orders.show', compact('order', 'sessions'));
    }

    /**
     * Setujui pembayaran pesanan.
     */
    public function approve(Request $request, Order $order): RedirectResponse
    {
        try {
            $order->approve($request->user());

            return back()->with('success', "Pesanan {$order->code} berhasil disetujui. Kabari pelanggan lewat tombol WhatsApp di bawah.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tolak bukti pembayaran pesanan dengan catatan alasan.
     */
    public function reject(RejectOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $order->reject($request->user(), $request->validated('reason'));

            return back()->with('success', "Bukti pembayaran pesanan {$order->code} telah ditolak. Kabari pelanggan lewat tombol WhatsApp di bawah.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Tandai pesanan telah selesai dikerjakan oleh teknisi.
     */
    public function complete(Request $request, Order $order): RedirectResponse
    {
        try {
            $order->complete($request->user());

            return back()->with('success', "Pesanan {$order->code} telah ditandai selesai. Kabari pelanggan lewat tombol WhatsApp di bawah.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Batalkan pesanan secara manual (wajib alasan).
     */
    public function cancel(CancelOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $order->cancel($request->user(), $request->validated('alasan_batal'));

            return back()->with('success', "Pesanan {$order->code} telah dibatalkan. Kabari pelanggan lewat tombol WhatsApp di bawah.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Ubah tanggal & sesi kunjungan. Kuota dicek kecuali admin mencentang "paksa".
     */
    public function reschedule(RescheduleOrderRequest $request, Order $order): RedirectResponse
    {
        try {
            $order->reschedule(
                $request->user(),
                $request->validated('jadwal_tanggal'),
                $request->validated('jadwal_sesi'),
                $request->boolean('paksa'),
            );

            return back()->with('success', "Jadwal pesanan {$order->code} berhasil diubah. Kabari pelanggan lewat tombol WhatsApp di bawah.");
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Stream gambar bukti transfer secara aman (hanya admin login).
     */
    public function streamProof(Payment $payment): StreamedResponse
    {
        $disk = Storage::disk('private');

        if (! $disk->exists($payment->proof_path)) {
            abort(404, 'File bukti transfer tidak ditemukan.');
        }

        // Hanya kirim jika isinya benar-benar gambar yang diizinkan (dicek dari isi file,
        // bukan nama file), agar file berbahaya tidak pernah dirender browser.
        $mime = $disk->mimeType($payment->proof_path);
        abort_unless(in_array($mime, self::PROOF_MIME_TYPES, true), 404);

        return $disk->response($payment->proof_path, null, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline',
            'Content-Security-Policy' => "default-src 'none'; img-src 'self'; style-src 'unsafe-inline'; sandbox",
            'Cache-Control' => 'no-store, private',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
