<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Exceptions\UniqueCodeExhaustedException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\KategoriLayanan;
use App\Models\Order;
use App\Services\BookingSchedule;
use App\Services\OrderPriceCalculator;
use App\Services\UniqueCodeAllocator;
use DomainException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use InvalidArgumentException;

class OrderController extends Controller
{
    /**
     * Berapa kali mencoba ulang jika kode unik bentrok dengan pesanan lain yang dibuat bersamaan.
     */
    private const UNIQUE_CODE_ATTEMPTS = 5;

    /**
     * Tampilkan formulir pemilihan layanan dan pengisian data order.
     */
    public function create(BookingSchedule $schedule): View
    {
        // Kategori + jenis pekerjaan yang bisa dipesan (aktif dan harga sudah diisi).
        $katalog = KategoriLayanan::katalog()->map(fn (KategoriLayanan $kategori) => [
            'id' => $kategori->id,
            'nama' => $kategori->nama,
            'catatan_harga' => $kategori->catatan_harga,
            'pekerjaan' => $kategori->services->map(fn ($service) => [
                'id' => $service->id,
                'nama' => $service->name,
                'harga' => $service->price,
                'harga_mulai' => $service->harga_mulai,
                'satuan' => $service->satuan,
                'label_harga' => $service->priceLabel(),
                'label_jumlah' => $service->qtyLabel(),
            ])->values(),
        ])->values();

        $sessions = $schedule->sessions();
        $availability = $schedule->availability();
        $maxDate = now()->addDays($schedule->maxDays())->toDateString();

        return view('order.index', compact('katalog', 'sessions', 'availability', 'maxDate'));
    }

    /**
     * Simpan order baru setelah total dihitung ulang di server.
     */
    public function store(
        StoreOrderRequest $request,
        OrderPriceCalculator $calculator,
        UniqueCodeAllocator $codes,
        BookingSchedule $schedule,
    ): RedirectResponse {
        $date = $request->validated('schedule_date');
        $sesi = $request->validated('sesi');

        for ($attempt = 1; $attempt <= self::UNIQUE_CODE_ATTEMPTS; $attempt++) {
            try {
                $order = DB::transaction(function () use ($request, $calculator, $codes, $schedule, $date, $sesi) {
                    // Cek ulang kuota di dalam transaksi (dengan kunci baris) agar
                    // pesanan yang masuk bersamaan tidak melebihi kuota sesi.
                    if ($schedule->isFull($date, $sesi, null, lock: true)) {
                        throw new DomainException($schedule->fullMessage($date, $sesi));
                    }

                    // Hitung ulang harga murni di server, dengan kode unik yang belum dipakai pesanan aktif.
                    $calculated = $calculator->calculate($request->validated('items'), $codes->pick());

                    $order = Order::create([
                        'code' => Order::generateCode(),
                        'name' => $request->validated('name'),
                        'phone' => $request->validated('phone'),
                        'address' => $request->validated('address'),
                        'schedule_date' => $date,
                        'sesi' => $sesi,
                        'notes' => $request->validated('notes'),
                        'subtotal' => $calculated['subtotal'],
                        'unique_code' => $calculated['unique_code'],
                        'kode_unik_aktif' => $calculated['unique_code'],
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
            } catch (UniqueConstraintViolationException $e) {
                // Kode unik (atau kode pesanan) baru saja dipakai pesanan lain: coba lagi.
                continue;
            } catch (InvalidArgumentException $e) {
                // Layanan dinonaktifkan admin tepat setelah validasi lolos.
                return back()
                    ->withInput()
                    ->withErrors(['items' => 'Salah satu layanan yang dipilih sudah tidak tersedia. Silakan pilih ulang layanan Anda.']);
            } catch (UniqueCodeExhaustedException $e) {
                return back()->withInput()->withErrors(['items' => $e->getMessage()]);
            } catch (DomainException $e) {
                return back()->withInput()->withErrors(['sesi' => $e->getMessage()]);
            }
        }

        return back()
            ->withInput()
            ->withErrors(['items' => 'Pesanan gagal dibuat karena sistem sedang sibuk. Silakan coba kirim ulang.']);
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
