<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Films extends Model
{
    protected $table = 'film';

    protected $fillable = [
        'judul',
        'kategori',
        'jadwal',
        'harga',
        'status'
    ];
}
