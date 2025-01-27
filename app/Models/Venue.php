<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $table = 'venues';

    protected $fillable = [
        'admin_id',
        'pertandingan_id',
        'nama',
        'alamat',
        'kapasitas',
        'fasilitas',
        'kota',
        'foto',
        'kontak',
        'status',
    ];

    protected $casts = [
        'fasilitas' => 'array', // Cast JSON ke array
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function pertandingan()
    {
        return $this->belongsToMany(Pertandingan::class, 'pertandingan_venue', 'venue_id', 'pertandingan_id');
    }
}
