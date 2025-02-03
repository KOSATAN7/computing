<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('pertandingan_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('nama');
            $table->text('alamat');
            $table->integer('kapasitas');
            $table->json('fasilitas')->nullable();
            $table->string('kota');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('foto_utama')->nullable();
            $table->json('foto_foto')->nullable();
            $table->string('video')->nullable();
            $table->string('kontak');
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('tidak_aktif');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pertandingan_id')->references('id')->on('pertandingan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
