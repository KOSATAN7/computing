<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertandingan extends Model
{
    use HasFactory;

    protected $table = 'pertandingan';

    protected $fillable = [
        'cabang_olahraga',
        'liga',
        'foto',
        'tim_tuan_rumah',
        'logo_tuan_rumah',
        'tim_tamu',
        'logo_tamu',
        'tanggal_pertandingan',
        'waktu_pertandingan',
    ];

    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'pertandingan_venue', 'pertandingan_id', 'venue_id');
    }
}
