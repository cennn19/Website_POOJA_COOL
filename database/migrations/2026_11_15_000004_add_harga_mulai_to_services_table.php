<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // true = harga tampil sebagai "Mulai dari Rp ..." (biaya sparepart dibayar terpisah di lokasi).
            $table->boolean('harga_mulai')->default(false)->after('price');
        });

        // Layanan perbaikan/pengecekan: harga baru pasti setelah dicek teknisi.
        DB::table('services')
            ->where('slug', 'like', 'perbaikan-%')
            ->orWhere('slug', 'like', 'pengecekan-%')
            ->update(['harga_mulai' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('harga_mulai');
        });
    }
};
