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
