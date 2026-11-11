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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique()->index();
            $table->string('name');
            $table->string('phone', 30);
            $table->text('address');
            $table->date('schedule_date');
            $table->text('notes')->nullable();
            $table->unsignedInteger('subtotal');
            $table->unsignedSmallInteger('unique_code');
            $table->unsignedInteger('total');
            $table->string('status', 32)->default('menunggu_pembayaran')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
