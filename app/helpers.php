<?php

if (! function_exists('format_rupiah')) {
    /**
     * Format angka ke format mata uang Rupiah. Contoh: Rp 1.250.000
     */
    function format_rupiah(int|float $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}

if (! function_exists('mask_phone')) {
    /**
     * Samarkan nomor telepon untuk halaman publik. Contoh: 0812****7890
     */
    function mask_phone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        if (strlen($digits) <= 7) {
            return str_repeat('*', strlen($digits));
        }

        return substr($digits, 0, 4).str_repeat('*', strlen($digits) - 8).substr($digits, -4);
    }
}

if (! function_exists('mask_address')) {
    /**
     * Samarkan alamat untuk halaman publik: hanya bagian terakhir (biasanya kota/kecamatan)
     * yang ditampilkan. Contoh: "Jl. Melati No. 5, Lowokwaru, Malang" → "•••, Malang"
     */
    function mask_address(?string $address): string
    {
        $parts = array_values(array_filter(array_map('trim', explode(',', (string) $address))));

        if (count($parts) > 1) {
            return '•••, '.end($parts);
        }

        $words = preg_split('/\s+/', trim((string) $address)) ?: [];

        return $words ? $words[0].' •••' : '•••';
    }
}

if (! function_exists('mask_name')) {
    /**
     * Samarkan nama: nama depan utuh, sisanya inisial. Contoh: "Hendra Wijaya" → "Hendra W."
     */
    function mask_name(?string $name): string
    {
        $words = preg_split('/\s+/', trim((string) $name)) ?: [];
        $first = array_shift($words) ?? '';
        $initials = array_map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)).'.', array_filter($words));

        return trim($first.' '.implode(' ', $initials));
    }
}
