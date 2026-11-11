<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadPaymentProofRequest;
use App\Models\Order;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman pembayaran QRIS dan form upload bukti bayar.
     */
    public function show(string $code): View
    {
        $order = Order::where('code', $code)
            ->with(['items', 'latestPayment'])
            ->firstOrFail();

        return view('payment.index', compact('order'));
    }

    /**
     * Unggah bukti pembayaran pelanggan ke penyimpanan private.
     */
    public function uploadProof(UploadPaymentProofRequest $request, string $code): RedirectResponse
    {
        $order = Order::where('code', $code)->firstOrFail();

        if (! $order->canUploadProof()) {
            return back()->with('error', 'Pesanan ini tidak dapat menerima unggahan bukti transfer pada status saat ini.');
        }

        try {
            // Simpan gambar bukti di disk private
            $file = $request->file('proof');
            $path = $file->store('payments', 'private');

            $order->recordPaymentProof($path);

            return redirect()
                ->route('payment.show', ['code' => $order->code])
                ->with('success', 'Bukti pembayaran berhasil diunggah! Tim kami akan segera memverifikasi pesanan Anda.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
