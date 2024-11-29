<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';
    protected $fillable = [
        'nama',
        'alamat',
        'kontak',
        'kota',
        'fasilitas',
        'status',
        'kapasitas',
        'foto',
        'video',
    ];

    public function films()
{
    return $this->belongsToMany(Films::class, 'film_venue', 'venue_id', 'film_id');
}
}