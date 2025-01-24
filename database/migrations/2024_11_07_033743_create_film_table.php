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
    Schema::create('films', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->string('judul'); // Judul film
        $table->string('kategori'); // Kategori film
        $table->date('jadwal'); // Tanggal jadwal film
        $table->integer('harga'); // Harga tiket
        $table->enum('status', ['comingsoon', 'ongoing', 'outdate'])->default('comingsoon'); // Status film
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
