<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVenuesTable extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); 
            $table->string('nama');
            $table->text('alamat');
            $table->integer('kapasitas');
            $table->json('fasilitas')->nullable(); 
            $table->string('kota'); 
            $table->string('foto')->nullable();
            $table->string('video')->nullable();
            $table->string('kontak'); 
            $table->enum('status', ['tersedia', 'tidak_tersedia'])->default('tersedia'); // Default ke tersedia
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
}
