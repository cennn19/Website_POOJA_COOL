<?php

namespace Database\Seeders;

use App\Enums\ServiceType;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Paket
            [
                'name' => 'Paket Cuci AC Hemat (2 Unit)',
                'slug' => 'paket-cuci-ac-hemat-2-unit',
                'type' => ServiceType::PACKAGE,
                'price' => 120000,
                'description' => 'Pembersihan unit indoor dan outdoor untuk 2 unit AC split, pembersihan filter, semprot drainase, serta pengecekan suhu.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Paket Cuci Komplit + Tambah Freon (1 Unit)',
                'slug' => 'paket-cuci-komplit-tambah-freon-1-unit',
                'type' => ServiceType::PACKAGE,
                'price' => 210000,
                'description' => 'Cuci menyeluruh evaporator & kondensor plus penambahan freon R32/R410A hingga tekanan optimal standar pabrik.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Paket Overhaul / Turun Unit Total',
                'slug' => 'paket-overhaul-turun-unit-total',
                'type' => ServiceType::PACKAGE,
                'price' => 450000,
                'description' => 'Pembongkaran total indoor, cuci kimia khusus kerak jamur dan debu tebal, flushing pipa pembuangan, dan vakum ulang.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Paket Perawatan Komersial / Kantor (4 Unit)',
                'slug' => 'paket-perawatan-komersial-kantor-4-unit',
                'type' => ServiceType::PACKAGE,
                'price' => 260000,
                'description' => 'Perawatan berkala AC split/cassette ruko & kantor. Cek kelistrikan, arus ampere, dan kebersihan sistem secara menyeluruh.',
                'is_active' => true,
                'sort_order' => 4,
            ],

            // Satuan
            [
                'name' => 'Cuci AC Split (0.5 - 2 PK)',
                'slug' => 'cuci-ac-split-05-2-pk',
                'type' => ServiceType::UNIT,
                'price' => 65000,
                'description' => 'Cuci filter, evaporator indoor, kipas blower, dan kondensor outdoor. Termasuk cek kebocoran dan suhu dingin.',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Tambah Freon R32 / R410A',
                'slug' => 'tambah-freon-r32-r410a',
                'type' => ServiceType::UNIT,
                'price' => 150000,
                'description' => 'Pengisian tambahan refrigeran ramah lingkungan R32 / R410A untuk AC yang kurang dingin.',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'name' => 'Isi Freon Full dari Kosong R32 / R410A',
                'slug' => 'isi-freon-full-dari-kosong-r32-r410a',
                'type' => ServiceType::UNIT,
                'price' => 250000,
                'description' => 'Vakum sistem pendingin, periksa kebocoran nepel/pipa, dan isi freon murni dari kondisi kosong hingga penuh.',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'name' => 'Pasang AC Baru / Bekas (0.5 - 1.5 PK)',
                'slug' => 'pasang-ac-baru-bekas-05-15-pk',
                'type' => ServiceType::UNIT,
                'price' => 250000,
                'description' => 'Instalasi rapi unit indoor & outdoor, proses vakum instalasi pipa berstandar SNI, serta pengetesan fungsi.',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'name' => 'Bongkar Pasang AC (Relokasi)',
                'slug' => 'bongkar-pasang-ac-relokasi',
                'type' => ServiceType::UNIT,
                'price' => 375000,
                'description' => 'Pump down penguncian freon, pembongkaran aman di lokasi lama, dan pemasangan kembali di lokasi baru.',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'name' => 'Perbaikan Kebocoran Air / Pipa Mampet',
                'slug' => 'perbaikan-kebocoran-air-pipa-mampet',
                'type' => ServiceType::UNIT,
                'price' => 120000,
                'description' => 'Mengatasi AC menetes atau banjir ke dalam ruangan, flushing lumut di selang drain, serta pembetulan kemiringan talang air.',
                'is_active' => true,
                'sort_order' => 15,
            ],
            [
                'name' => 'Pengecekan Kerusakan / Troubleshooting',
                'slug' => 'pengecekan-kerusakan-troubleshooting',
                'type' => ServiceType::UNIT,
                'price' => 75000,
                'description' => 'Diagnosa menyeluruh komponen elektrikal, modul PCB, kapasitor, dan kompresor jika AC mati atau berisik.',
                'is_active' => true,
                'sort_order' => 16,
            ],
        ];

        foreach ($services as $data) {
            Service::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
