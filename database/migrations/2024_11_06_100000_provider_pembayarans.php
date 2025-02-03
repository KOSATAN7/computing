<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tabel venues sudah ada sebelum membuat provider_pembayarans
        if (!Schema::hasTable('venues')) {
            throw new Exception("Table 'venues' belum dibuat. Jalankan migrasi venues terlebih dahulu.");
        }

        Schema::create('provider_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metode_pembayaran_id')
                ->constrained('metode_pembayarans')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('venue_id')
                ->constrained('venues')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('nama', 255);
            $table->string('no_rek', 50);
            $table->string('penerima', 255);
            $table->string('deskripsi', 3000)->nullable();
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Tambahkan foreign key di venues ke provider_pembayarans
        Schema::table('venues', function (Blueprint $table) {
            $table->foreign('provider_id')
                ->references('id')->on('provider_pembayarans')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropForeign(['provider_id']);
        });

        Schema::dropIfExists('provider_pembayarans');
    }
};
