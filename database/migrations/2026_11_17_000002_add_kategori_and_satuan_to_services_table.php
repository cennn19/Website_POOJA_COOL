<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel services = "jenis pekerjaan". Kolom urutan memakai sort_order yang sudah ada,
     * dan status aktif memakai is_active yang sudah ada.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('kategori_layanan_id')
                ->nullable()
                ->after('id')
                ->constrained('kategori_layanan')
                ->nullOnDelete();
            // unit atau meter
            $table->string('satuan', 10)->default('unit')->after('type');
        });

        // Migrasi data: layanan lama (Maintenance, Perbaikan, Pemasangan, paket, dll.) tidak
        // punya padanan pasti di struktur baru, jadi dinonaktifkan, BUKAN dihapus.
        // Pesanan lama tetap valid karena order_items menyimpan nama & harga saat dipesan.
        DB::table('services')
            ->whereNull('kategori_layanan_id')
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kategori_layanan_id');
            $table->dropColumn('satuan');
        });
    }
};
