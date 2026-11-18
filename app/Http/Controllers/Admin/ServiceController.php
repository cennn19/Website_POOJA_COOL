<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateServicesRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateKategoriLayananRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\KategoriLayanan;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Daftar jenis pekerjaan dikelompokkan per kategori.
     */
    public function index(): View
    {
        $kategoriLayanan = KategoriLayanan::query()
            ->orderBy('urutan')
            ->with('services')
            ->get();

        // Layanan lama sebelum struktur kategori: tetap ditampilkan (nonaktif) agar riwayat jelas.
        $layananLama = Service::query()
            ->whereNull('kategori_layanan_id')
            ->orderBy('sort_order')
            ->get();

        return view('admin.services.index', compact('kategoriLayanan', 'layananLama'));
    }

    /**
     * Simpan perubahan harga, satuan, "Mulai dari", status aktif, dan urutan sebuah jenis pekerjaan.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $data = [
            'price' => $request->validated('price'),
            'harga_mulai' => $request->boolean('harga_mulai'),
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->has('satuan')) {
            $data['satuan'] = $request->validated('satuan');
        }

        if ($request->has('sort_order')) {
            $data['sort_order'] = $request->validated('sort_order');
        }

        $service->update($data);

        return back()->with('success', "Layanan \"{$service->name}\" berhasil diperbarui.");
    }

    /**
     * Simpan semua baris di halaman Layanan & Harga sekaligus.
     */
    public function bulkUpdate(BulkUpdateServicesRequest $request): RedirectResponse
    {
        $rows = $request->validated('services');

        $diubah = DB::transaction(function () use ($rows) {
            $services = Service::query()->whereKey(array_keys($rows))->get();
            $diubah = 0;

            foreach ($services as $service) {
                $row = $rows[$service->id];

                $service->fill([
                    'price' => (int) $row['price'],
                    'satuan' => $row['satuan'],
                    'harga_mulai' => filter_var($row['harga_mulai'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'is_active' => filter_var($row['is_active'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'sort_order' => (int) $row['sort_order'],
                ]);

                if ($service->isDirty()) {
                    $service->save();
                    $diubah++;
                }
            }

            return $diubah;
        });

        return back()->with('success', $diubah > 0
            ? "{$diubah} jenis pekerjaan berhasil diperbarui."
            : 'Tidak ada perubahan yang disimpan.');
    }

    /**
     * Tambah jenis pekerjaan baru di kategori yang sudah ada.
     */
    public function store(StoreServiceRequest $request, KategoriLayanan $kategori): RedirectResponse
    {
        $name = $request->validated('name');

        $kategori->services()->create([
            'name' => $name,
            'slug' => $this->uniqueSlug($kategori->slug.'-'.$name),
            'type' => 'unit',
            'satuan' => $request->validated('satuan'),
            'price' => $request->validated('price'),
            'harga_mulai' => $request->boolean('harga_mulai'),
            'is_active' => true,
            'sort_order' => $request->validated('sort_order') ?? ((int) $kategori->services()->max('sort_order') + 1),
        ]);

        return back()->with('success', "Jenis pekerjaan \"{$name}\" ditambahkan ke {$kategori->nama}.");
    }

    /**
     * Ubah catatan harga sebuah kategori.
     */
    public function updateKategori(UpdateKategoriLayananRequest $request, KategoriLayanan $kategori): RedirectResponse
    {
        $kategori->update([
            'catatan_harga' => $request->validated('catatan_harga') ?: null,
        ]);

        return back()->with('success', "Catatan harga \"{$kategori->nama}\" berhasil diperbarui.");
    }

    private function uniqueSlug(string $text): string
    {
        $base = Str::slug($text) ?: 'layanan';
        $slug = $base;

        for ($i = 2; Service::where('slug', $slug)->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
