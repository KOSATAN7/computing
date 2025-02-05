<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('venue_id')->constrained('venues')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->constrained('provider_pembayarans')->onDelete('set null'); // Ubah ke provider_pembayarans
            $table->integer('jumlah_orang');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', ['menunggu', 'berhasil', 'dibatalkan'])->default('menunggu');
            $table->timestamps();
        });

        Schema::create('booking_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('menu_id')->constrained('menus')->onDelete('cascade');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('booking_menu');
        Schema::dropIfExists('bookings');
    }
};
