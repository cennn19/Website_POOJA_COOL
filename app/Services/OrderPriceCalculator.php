<?php

namespace App\Services;

use App\Models\Service;
use InvalidArgumentException;

class OrderPriceCalculator
{
    /**
     * Hitung ulang total pesanan secara aman berdasarkan harga resmi di database.
     *
     * @param  array<array{service_id: int|string, qty: int|string, kategori_id?: int|string}>  $itemsInput
     * @param  int|null  $uniqueCode  Opsional, jika null akan di-generate acak (1-999)
     * @return array{
     *     items: array<array{
     *         service_id: int,
     *         service_name: string,
     *         qty: int,
     *         price_at_order: int,
     *         subtotal: int
     *     }>,
     *     subtotal: int,
     *     unique_code: int,
     *     total: int
     * }
     */
    public function calculate(array $itemsInput, ?int $uniqueCode = null): array
    {
        if (empty($itemsInput)) {
            throw new InvalidArgumentException('Minimal harus memilih 1 layanan.');
        }

        $serviceIds = array_column($itemsInput, 'service_id');
        // Hanya layanan yang bisa dipesan: aktif, harga sudah diisi (> 0), kategori aktif.
        $services = Service::bookable()
            ->with('kategori')
            ->whereIn('id', $serviceIds)
            ->get()
            ->keyBy('id');

        $calculatedItems = [];
        $subtotal = 0;

        foreach ($itemsInput as $item) {
            $serviceId = (int) ($item['service_id'] ?? 0);
            $qty = (int) ($item['qty'] ?? 0);

            if ($qty < 1 || $qty > 20) {
                throw new InvalidArgumentException('Jumlah (qty) layanan harus antara 1 dan 20.');
            }

            $service = $services->get($serviceId);
            if (! $service) {
                throw new InvalidArgumentException("Layanan dengan ID {$serviceId} tidak ditemukan atau sedang tidak aktif.");
            }

            if (isset($item['kategori_id']) && (int) $item['kategori_id'] !== (int) $service->kategori_layanan_id) {
                throw new InvalidArgumentException('Jenis pekerjaan tidak sesuai dengan kategori yang dipilih.');
            }

            $itemPrice = (int) $service->price;
            $itemSubtotal = $itemPrice * $qty;
            $subtotal += $itemSubtotal;

            $calculatedItems[] = [
                'service_id' => $service->id,
                'service_name' => $service->fullName(),
                'qty' => $qty,
                'price_at_order' => $itemPrice,
                'subtotal' => $itemSubtotal,
            ];
        }

        $code = $uniqueCode ?? random_int(1, 999);
        $total = $subtotal + $code;

        return [
            'items' => $calculatedItems,
            'subtotal' => $subtotal,
            'unique_code' => $code,
            'total' => $total,
        ];
    }
}
