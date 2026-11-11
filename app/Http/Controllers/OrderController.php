<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Service;
use App\Services\OrderPriceCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan formulir pemilihan layanan dan pengisian data order.
     */
    public function create(): View
    {
        $packages = Service::active()
            ->packages()
            ->orderBy('sort_order')
            ->get();

        $units = Service::active()
            ->units()
            ->orderBy('sort_order')
            ->get();

        return view('order.index', compact('packages', 'units'));
    }

    /**
     * Simpan order baru setelah total dihitung ulang di server.
     */
    public function store(StoreOrderRequest $request, OrderPriceCalculator $calculator): RedirectResponse
    {
        // Hitung ulang harga murni di server mengabaikan request client
        $calculated = $calculator->calculate($request->validated('items'));

        $order = DB::transaction(function () use ($request, $calculated) {
            $order = Order::create([
                'code' => Order::generateCode(),
                'name' => $request->validated('name'),
                'phone' => $request->validated('phone'),
                'address' => $request->validated('address'),
                'schedule_date' => $request->validated('schedule_date'),
                'notes' => $request->validated('notes'),
                'subtotal' => $calculated['subtotal'],
                'unique_code' => $calculated['unique_code'],
                'total' => $calculated['total'],
                'status' => OrderStatus::MENUNGGU_PEMBAYARAN,
            ]);

            foreach ($calculated['items'] as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        return redirect()
            ->route('payment.show', ['code' => $order->code])
            ->with('success', 'Pesanan Anda berhasil dibuat! Silakan lakukan pembayaran dan unggah bukti transfer.');
    }

    /**
     * Cek status pesanan pelanggan berdasarkan kode order.
     */
    public function status(Request $request, ?string $code = null): View
    {
        $orderCode = $code ?? $request->query('code');
        $order = null;

        if ($orderCode) {
            $order = Order::where('code', trim($orderCode))
                ->with(['items.service', 'latestPayment'])
                ->first();
        }

        return view('status.index', [
            'order' => $order,
            'searchedCode' => $orderCode,
        ]);
    }
}
