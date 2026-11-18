<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 'pagi' atau 'siang'. Nullable agar pesanan lama (sebelum ada sesi) tetap valid.
            $table->string('sesi', 10)->nullable()->after('schedule_date');
            $table->index(['schedule_date', 'sesi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['schedule_date', 'sesi']);
            $table->dropColumn('sesi');
        });
    }
};
