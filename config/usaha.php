<?php

/*
|--------------------------------------------------------------------------
| Data Usaha
|--------------------------------------------------------------------------
|
| Satu-satunya sumber data usaha untuk seluruh website: landing page,
| kontak, footer, halaman pembayaran, dan pesan WhatsApp otomatis.
|
| Nilai berawalan "ISI_" BELUM FINAL dan akan tampil apa adanya di website.
| Setelah mengubah file ini di server produksi yang memakai config cache,
| jalankan: php artisan config:clear
|
*/

return [

    'nama' => 'Pooja Cool Prima',

    'nama_legal' => 'CV Pooja Cool Prima',

    // Nomor telepon untuk ditampilkan sebagai teks.
    'telepon' => '0852-3285-5466',

    // Format internasional untuk tautan wa.me.
    // TODO: pastikan nomor 0852-3285-5466 aktif di WhatsApp.
    'whatsapp' => env('USAHA_WHATSAPP', '6285232855466'),

    'email' => env('USAHA_EMAIL', 'idhwanmuhammad1703@gmail.com'),

    'alamat' => 'Dusun Grogol RT 04 RW 02, Desa Tritunggal, Kecamatan Babat, Kabupaten Lamongan',

    // Lokasi singkat usaha (dari alamat di atas), dipakai di judul & deskripsi halaman.
    'lokasi' => 'Babat, Lamongan',

    // TODO: tunggu konfirmasi area layanan (contoh: "Babat dan sekitarnya, Kabupaten Lamongan").
    'area_layanan' => 'Babat, Tikung, Deket dan Area di sekitar Kabupaten Lamongan',

    // TODO: tunggu konfirmasi jam operasional.
    'jam_operasional' => '07.00 - 18.00',

    // TODO: ganti dengan nama pemilik/merchant yang tercetak di QRIS asli
    'qris_nama_pemilik' => 'Idhwan Muhammad',

];
