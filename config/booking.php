<?php

/*
|--------------------------------------------------------------------------
| Aturan Jadwal Kunjungan
|--------------------------------------------------------------------------
|
| Setelah mengubah file ini di server produksi yang memakai config cache,
| jalankan: php artisan config:clear
|
*/

return [

    // Jumlah maksimal pesanan aktif (selain "dibatalkan") per tanggal + sesi.
    'kuota_per_sesi' => (int) env('BOOKING_KUOTA_PER_SESI', 3),

    // Tanggal kunjungan paling jauh yang boleh dipilih pelanggan (hari dari hari ini).
    'maks_hari_ke_depan' => 30,

    // Dibatalkan otomatis setelah sekian jam: pesanan "menunggu_pembayaran" tanpa bukti bayar
    // (dihitung dari pesanan dibuat) dan pesanan "ditolak" tanpa unggah ulang (dihitung dari saat ditolak).
    'kedaluwarsa_jam' => 24,

    // Untuk hosting tanpa cron: pengecekan pesanan kedaluwarsa dijalankan saat ada pengunjung,
    // paling sering sekali per sekian menit agar website tetap ringan.
    'cek_kedaluwarsa_menit' => 5,

    /*
     | Sesi kunjungan.
     | 'batas_pesan_hari_ini' => jam terakhir sesi ini boleh dipesan untuk HARI INI
     |                           (format H:i). null = tidak bisa dipesan untuk hari ini.
     */
    'sesi' => [
        'pagi' => [
            'label' => 'Pagi',
            'jam' => '08.00–12.00',
            'batas_pesan_hari_ini' => null,
        ],
        'siang' => [
            'label' => 'Siang',
            'jam' => '13.00–17.00',
            'batas_pesan_hari_ini' => '10:00',
        ],
    ],

];
