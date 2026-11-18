<?php

namespace Database\Seeders;

use App\Models\KategoriLayanan;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * 5 kategori + 16 jenis pekerjaan Pooja Cool Prima (sumber: datausaha.md).
 *
 * Harga belum diketahui, jadi diisi 0. Layanan berharga 0 TIDAK tampil di website
 * dan tidak bisa dipesan sampai admin mengisi harganya di halaman Layanan & Harga.
 *
 * Seeder aman dijalankan ulang: data yang sudah ada (termasuk harga yang sudah
 * diisi admin) tidak ditimpa.
 */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->katalog() as $urutanKategori => $kategori) {
            $model = KategoriLayanan::firstOrCreate(
                ['slug' => $kategori['slug']],
                [
                    'nama' => $kategori['nama'],
                    'urutan' => $urutanKategori + 1,
                    'catatan_harga' => $kategori['catatan_harga'],
                    'aktif' => true,
                ],
            );

            foreach ($kategori['pekerjaan'] as $urutan => [$slug, $nama, $satuan, $hargaMulai]) {
                Service::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'kategori_layanan_id' => $model->id,
                        'name' => $nama,
                        'type' => 'unit',
                        'satuan' => $satuan,
                        'price' => 0, // TODO: harga asli belum ada, diisi admin lewat Layanan & Harga
                        'harga_mulai' => $hargaMulai,
                        'is_active' => true,
                        'sort_order' => $urutan + 1,
                    ],
                );
            }
        }
    }

    /**
     * @return array<int, array{nama: string, slug: string, catatan_harga: ?string, pekerjaan: array<int, array{0: string, 1: string, 2: string, 3: bool}>}>
     */
    private function katalog(): array
    {
        return [
            [
                'nama' => 'Cleaning AC',
                'slug' => 'cleaning-ac',
                'catatan_harga' => null,
                'pekerjaan' => [
                    ['cleaning-ac-split-05-1-pk', 'AC Split 0,5–1 PK', 'unit', false],
                    ['cleaning-ac-split-15-2-pk', 'AC Split 1,5–2 PK', 'unit', false],
                    ['cleaning-ac-split-cassette-standing-floor', 'AC Split/Cassette/Standing Floor', 'unit', false],
                ],
            ],
            [
                'nama' => 'Pasang AC (Lokasi Standar)',
                'slug' => 'pasang-ac-lokasi-standar',
                'catatan_harga' => 'Harga untuk lokasi standar dengan pipa sampai 3 meter. Kelebihan pipa dihitung per meter. Tarif dapat menyesuaikan kondisi lokasi pemasangan dan dikonfirmasi teknisi sebelum pekerjaan dimulai.',
                'pekerjaan' => [
                    ['pemasangan-ac-split-05-1-pk', 'Pemasangan AC Split 0,5–1 PK', 'unit', true],
                    ['pemasangan-ac-split-15-2-pk', 'Pemasangan AC Split 1,5–2 PK', 'unit', true],
                    ['pemasangan-standing-floor-cassette', 'Pemasangan Standing Floor-Cassette', 'unit', true],
                    ['bongkar-ac-split', 'Bongkar AC Split', 'unit', true],
                    ['instalasi-pipa-lebih-dari-3-meter', 'Instalasi pipa lebih dari 3 meter', 'meter', false],
                ],
            ],
            [
                'nama' => 'Instalasi Listrik',
                'slug' => 'instalasi-listrik',
                'catatan_harga' => 'Tarif menyesuaikan kondisi instalasi di lokasi dan dikonfirmasi teknisi sebelum pekerjaan dimulai.',
                'pekerjaan' => [
                    ['instalasi-listrik-ac', 'Instalasi listrik AC', 'unit', true],
                ],
            ],
            [
                'nama' => 'Tambah Freon',
                'slug' => 'tambah-freon',
                'catatan_harga' => null,
                'pekerjaan' => [
                    ['tambah-freon-split-05-1-pk', 'Split 0,5–1 PK', 'unit', false],
                    ['tambah-freon-split-15-2-pk', 'Split 1,5–2 PK', 'unit', false],
                    ['tambah-freon-cassette-standing-floor', 'Cassette/Standing Floor', 'unit', false],
                    ['isi-freon-flushing', 'Isi Freon + Flushing', 'unit', false],
                ],
            ],
            [
                'nama' => 'Service dan Perbaikan AC',
                'slug' => 'service-dan-perbaikan-ac',
                'catatan_harga' => 'Tarif menyesuaikan tingkat kerusakan. Penggantian sparepart dibayar terpisah setelah dicek teknisi dan disetujui pelanggan.',
                'pekerjaan' => [
                    ['reparasi-ringan', 'Reparasi ringan (service modul ringan, ganti sensor, thermistor, dan lainnya)', 'unit', true],
                    ['ganti-kapasitor-overload', 'Ganti kapasitor/overload', 'unit', true],
                    ['vacuum-flushing-instalasi-lebih-dari-15-pk', 'Vacuum + Flushing instalasi unit lebih dari 1,5 PK', 'unit', true],
                ],
            ],
        ];
    }
}
