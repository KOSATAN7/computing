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
}