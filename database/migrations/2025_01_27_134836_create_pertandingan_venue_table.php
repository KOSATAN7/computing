<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePertandinganVenueTable extends Migration
{
    public function up(): void
    {
        Schema::create('pertandingan_venue', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pertandingan_id');
            $table->unsignedBigInteger('venue_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pertandingan_id')
                  ->references('id')
                  ->on('pertandingan')
                  ->onDelete('cascade');

            $table->foreign('venue_id')
                  ->references('id')
                  ->on('venues')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertandingan_venue');
    }
}
