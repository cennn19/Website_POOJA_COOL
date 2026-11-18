<?php

namespace App\Enums;

enum OrderStatus: string
{
    case MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case DISETUJUI = 'disetujui';
    case DITOLAK = 'ditolak';
    case SELESAI = 'selesai';
    case DIBATALKAN = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::DISETUJUI => 'Disetujui',
            self::DITOLAK => 'Ditolak',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
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
            self::DIBATALKAN => 'neutral',
        };
    }

    /**
     * Status yang dianggap sudah tidak aktif (kode unik boleh dipakai ulang).
     *
     * @return array<self>
     */
    public static function inactive(): array
    {
        return [self::SELESAI, self::DIBATALKAN];
    }

    public function isActive(): bool
    {
        return ! in_array($this, self::inactive(), true);
    }

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
