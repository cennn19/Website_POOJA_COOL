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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('alasan_batal')->nullable()->after('status');
            $table->timestamp('dibatalkan_pada')->nullable()->after('alasan_batal');

            // Salinan unique_code selama pesanan AKTIF; NULL saat selesai/dibatalkan.
            // Unique index ini mencegah dua pesanan aktif memakai kode unik yang sama
            // (MySQL mengizinkan banyak NULL pada kolom unique).
            $table->unsignedSmallInteger('kode_unik_aktif')->nullable()->unique()->after('unique_code');
        });

        // Isi kode_unik_aktif untuk pesanan aktif yang sudah ada.
        // Jika ada kode ganda dari data lama, hanya pesanan tertua yang mendapatkannya.
        $used = [];
        DB::table('orders')
            ->whereNotIn('status', ['selesai', 'dibatalkan'])
            ->orderBy('id')
            ->get(['id', 'unique_code'])
            ->each(function ($order) use (&$used) {
                if (isset($used[$order->unique_code])) {
                    return;
                }
                $used[$order->unique_code] = true;
                DB::table('orders')->where('id', $order->id)->update(['kode_unik_aktif' => $order->unique_code]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['kode_unik_aktif']);
            $table->dropColumn(['alasan_batal', 'dibatalkan_pada', 'kode_unik_aktif']);
        });
    }
};
