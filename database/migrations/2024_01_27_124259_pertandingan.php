<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pertandingan', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_olahraga');
            $table->string('liga');
            $table->string('tim_tuan_rumah');
            $table->string('logo_tuan_rumah');
            $table->string('tim_tamu');
            $table->string('logo_tamu');
            $table->date('tanggal_pertandingan');
            $table->time('waktu_pertandingan');
            $table->enum('status_pertandingan', ['upcoming', 'ongoing', 'completed'])->default('upcoming'); // Status pertandingan
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('tidak_aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertandingan');
    }
};
