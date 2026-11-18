<?php

namespace App\Support;

/**
 * Pembuat tautan wa.me dan format nomor WhatsApp Indonesia.
 */
class WhatsApp
{
    /**
     * Ubah nomor ke format internasional tanpa simbol: 0812-3456-7890 → 6281234567890.
     */
    public static function normalize(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }

    /**
     * Format tampilan lokal: 6281234567890 → 0812-3456-7890.
     */
    public static function display(?string $phone): string
    {
        $normalized = static::normalize($phone);

        if (! str_starts_with($normalized, '62')) {
            return (string) $phone;
        }

        $local = '0'.substr($normalized, 2);

        return trim(implode('-', [
            substr($local, 0, 4),
            substr($local, 4, 4),
            substr($local, 8),
        ]), '-');
    }

    /**
     * Tautan wa.me ke nomor tertentu, opsional dengan pesan terisi.
     */
    public static function link(?string $phone, ?string $message = null): string
    {
        $url = 'https://wa.me/'.static::normalize($phone);

        return $message ? $url.'?text='.rawurlencode($message) : $url;
    }

    /**
     * Nomor WhatsApp admin dari config/usaha.php.
     */
    public static function adminNumber(): string
    {
        return (string) config('usaha.whatsapp');
    }

    /**
     * Tautan wa.me ke admin, opsional dengan pesan terisi.
     */
    public static function adminLink(?string $message = null): string
    {
        return static::link(static::adminNumber(), $message);
    }
}
