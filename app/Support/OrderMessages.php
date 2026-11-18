<?php

namespace App\Support;

use App\Enums\OrderStatus;
use App\Models\Order;

/**
 * Templat pesan WhatsApp otomatis.
 * Ubah kalimat di sini jika ingin mengganti isi pesan.
 */
class OrderMessages
{
    /**
     * Pelanggan → admin: baru membuat pesanan, belum bayar.
     */
    public static function customerNewOrder(Order $order): string
    {
        return implode("\n", [
            'Halo Admin '.config('usaha.nama').', saya baru membuat pesanan.',
            '',
            'Kode: '.$order->code,
            'Nama: '.$order->name,
            'Layanan: '.static::itemsSummary($order),
            'Jadwal: '.$order->schedule_date->translatedFormat('l, d F Y').($order->sesiLabel() ? ', sesi '.$order->sesiLabel() : ''),
            'Total: '.format_rupiah($order->total),
            '',
            'Mohon konfirmasi ketersediaan teknisi di tanggal tersebut. Terima kasih.',
        ]);
    }

    /**
     * Pelanggan → admin: sudah bayar & unggah bukti.
     */
    public static function customerPaid(Order $order): string
    {
        $method = $order->latestPayment?->methodLabel();

        return implode("\n", array_filter([
            'Halo Admin '.config('usaha.nama').', saya sudah membayar dan mengunggah bukti pembayaran.',
            '',
            'Kode: '.$order->code,
            'Nama: '.$order->name,
            'Total: '.format_rupiah($order->total),
            $method ? 'Metode: '.$method : null,
            '',
            'Mohon diverifikasi. Terima kasih.',
        ], fn ($line) => $line !== null));
    }

    /**
     * Admin → pelanggan: pesan sesuai status pesanan saat ini.
     */
    public static function adminToCustomer(Order $order): string
    {
        $greeting = 'Halo '.static::firstName($order->name).', kami dari '.config('usaha.nama').'.';
        $statusUrl = route('order.status', ['code' => $order->code]);
        $paymentUrl = route('payment.show', ['code' => $order->code]);
        $date = $order->schedule_date->translatedFormat('l, d F Y').($order->sesiLabel() ? ', sesi '.$order->sesiLabel() : '');

        $body = match ($order->status) {
            OrderStatus::MENUNGGU_PEMBAYARAN => [
                'Pesanan Anda ('.$order->code.') sudah kami terima dan menunggu pembayaran sebesar '.format_rupiah($order->total).'.',
                '',
                'Silakan selesaikan pembayaran di: '.$paymentUrl,
            ],
            OrderStatus::MENUNGGU_VERIFIKASI => [
                'Bukti pembayaran untuk pesanan '.$order->code.' sudah kami terima dan sedang kami periksa.',
                'Kami akan mengabari Anda kembali setelah verifikasi selesai.',
            ],
            OrderStatus::DISETUJUI => [
                'Pembayaran untuk pesanan '.$order->code.' sudah kami VERIFIKASI.',
                '',
                'Jadwal kunjungan teknisi: '.$date.'.',
                'Teknisi akan menghubungi nomor ini sebelum berangkat. Mohon pastikan alamat kunjungan sudah benar.',
                '',
                'Status pesanan: '.$statusUrl,
            ],
            OrderStatus::DITOLAK => [
                'Mohon maaf, bukti pembayaran untuk pesanan '.$order->code.' belum dapat kami terima.',
                'Alasan: '.($order->latestPayment?->note ?? '-'),
                '',
                'Silakan unggah ulang bukti yang benar di: '.$paymentUrl,
            ],
            OrderStatus::DIBATALKAN => [
                'Pesanan '.$order->code.' telah DIBATALKAN.',
                'Alasan: '.($order->alasan_batal ?? '-'),
                '',
                'Jika ada pertanyaan atau ingin memesan ulang, silakan balas pesan ini.',
            ],
            OrderStatus::SELESAI => [
                'Pekerjaan servis untuk pesanan '.$order->code.' sudah selesai. Terima kasih telah menggunakan layanan kami.',
                'Jika keluhan yang sama muncul lagi dalam masa garansi, cukup balas pesan ini.',
            ],
        };

        return implode("\n", array_merge([$greeting, ''], $body));
    }

    /**
     * Ringkasan item, misal "Cuci AC Split (2x), Isi Freon (1x)".
     */
    protected static function itemsSummary(Order $order): string
    {
        return $order->items
            ->map(fn ($item) => $item->service_name.' ('.$item->qty.'x)')
            ->implode(', ');
    }

    protected static function firstName(string $name): string
    {
        return strtok(trim($name), ' ') ?: $name;
    }
}
