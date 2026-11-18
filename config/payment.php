<?php

/*
|--------------------------------------------------------------------------
| Metode Pembayaran
|--------------------------------------------------------------------------
|
| Ubah daftar di bawah ini untuk mengatur metode pembayaran yang tampil
| di halaman pembayaran pelanggan. SEMUA DATA DI BAWAH MASIH CONTOH,
| ganti dengan rekening / akun asli sebelum website dipakai.
| Nama pemilik QRIS diatur di config/usaha.php ('qris_nama_pemilik').
|
| Setiap metode:
|   'key'            => ID unik (huruf kecil, tanpa spasi). Disimpan di database,
|                       jadi jangan diganti setelah ada pembayaran yang memakainya.
|   'type'           => 'qris', 'bank', atau 'ewallet' (menentukan tab tampilan).
|   'name'           => Nama yang tampil, misal "BCA" atau "DANA".
|   'account_number' => Nomor rekening / nomor HP e-wallet / NMID untuk QRIS.
|   'account_name'   => Nama pemilik rekening / akun.
|   'image'          => (opsional) Path gambar di folder public, misal 'images/qris.jpeg'.
|                       Untuk QRIS, gambar ini adalah kode QR yang dipindai.
|   'note'           => (opsional) Catatan singkat untuk pelanggan.
|   'enabled'        => true = tampil, false = disembunyikan.
|
| Setelah mengubah file ini di server produksi yang memakai config cache,
| jalankan: php artisan config:clear
|
*/

return [

    'methods' => [

        // ---------------- QRIS ----------------
        [
            'key' => 'qris',
            'type' => 'qris',
            'name' => 'QRIS',
            'account_number' => null, // NMID (opsional, sudah tercetak di gambar QRIS)
            'account_name' => null,   // diambil dari config/usaha.php
            'image' => 'images/qris.jpeg',
            'note' => 'Bisa dibayar dari semua aplikasi m-banking dan e-wallet yang mendukung QRIS.',
            'enabled' => true,
        ],

        // ------------- TRANSFER BANK -------------
        [
            'key' => 'bca',
            'type' => 'bank',
            'name' => 'BCA',
            'account_number' => '85232855466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],
        [
            'key' => 'mandiri',
            'type' => 'bank',
            'name' => 'Bank Mandiri',
            'account_number' => '85232855466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],
        [
            'key' => 'bri',
            'type' => 'bank',
            'name' => 'BRI',
            'account_number' => '85232855466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],

        // ---------------- E-WALLET ----------------
        [
            'key' => 'dana',
            'type' => 'ewallet',
            'name' => 'DANA',
            'account_number' => '0852-3285-5466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],
        [
            'key' => 'gopay',
            'type' => 'ewallet',
            'name' => 'GoPay',
            'account_number' => '0852-3285-5466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],
        [
            'key' => 'ovo',
            'type' => 'ewallet',
            'name' => 'OVO',
            'account_number' => '0852-3285-5466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => true,
        ],
        [
            'key' => 'shopeepay',
            'type' => 'ewallet',
            'name' => 'ShopeePay',
            'account_number' => '0852-3285-5466', // TODO: ganti dengan data asli CV Pooja Cool Prima
            'account_name' => 'Idhwan Muhammad',
            'note' => null,
            'enabled' => false,
        ],
    ],

    // Judul tab untuk tiap jenis metode (urutan di sini = urutan tab).
    'types' => [
        'qris' => 'QRIS',
        'bank' => 'Transfer Bank',
        'ewallet' => 'E-Wallet',
    ],

];
