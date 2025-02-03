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
        Schema::create('provider_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metode_pembayaran_id')
                ->constrained('metode_pembayarans', 'id')
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
