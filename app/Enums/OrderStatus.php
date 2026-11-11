<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case SELESAI = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMBAYARAN => 'neutral',
            self::MENUNGGU_VERIFIKASI => 'warning',
            self::DISETUJUI => 'success',
            self::DITOLAK => 'danger',
            self::SELESAI => 'primary',
        };
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
