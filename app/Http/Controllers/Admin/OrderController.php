<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RejectOrderRequest;
use App\Models\Order;
use App\Models\Payment;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan dengan filter status, pencarian, dan paginasi.
     */
    public function index(Request $request): View
    {
        $query = Order::query()->with(['items', 'latestPayment']);

        if ($request->filled('status')) {
            $statusEnum = OrderStatus::tryFrom($request->status);
            if ($statusEnum) {
                $query->where('status', $statusEnum);
            }
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
            'total' => Order::count(),
            'menunggu_pembayaran' => Order::where('status', OrderStatus::MENUNGGU_PEMBAYARAN)->count(),
            'menunggu_verifikasi' => Order::where('status', OrderStatus::MENUNGGU_VERIFIKASI)->count(),
            'disetujui' => Order::where('status', OrderStatus::DISETUJUI)->count(),
            'ditolak' => Order::where('status', OrderStatus::DITOLAK)->count(),
            'selesai' => Order::where('status', OrderStatus::SELESAI)->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    /**
     * Tampilkan detail pesanan untuk verifikasi admin.
     */
    public function show(Order $order): View
    {
        $order->load(['items.service', 'payments.verifier']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Setujui pembayaran pesanan.
     */
    public function approve(Request $request, Order $order): RedirectResponse
    {
        try {
            $order->approve($request->user());

            return back()->with('success', "Pesanan {$order->code} berhasil disetujui!");
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

            return back()->with('success', "Bukti pembayaran pesanan {$order->code} telah ditolak.");
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

            return back()->with('success', "Pesanan {$order->code} telah ditandai selesai.");
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Stream gambar bukti transfer secara aman (hanya admin login).
     */
    public function streamProof(Payment $payment): StreamedResponse
    {
        if (! Storage::disk('private')->exists($payment->proof_path)) {
            abort(404, 'File bukti transfer tidak ditemukan.');
        }

        return Storage::disk('private')->response($payment->proof_path);
    }
}
