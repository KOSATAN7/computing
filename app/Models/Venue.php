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
        'foto_utama',
        'foto_foto',
        'kontak',
        'status',
    ];

    protected $casts = [
        'fasilitas' => 'array',
        'foto_foto' => 'array',
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
