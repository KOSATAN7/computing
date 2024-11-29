<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Films extends Model
{
    protected $table = 'films';

    protected $fillable = [
        'judul',
        'kategori', 
        'jadwal',
        'harga',
        'status'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori'); // 'kategori' adalah foreign key
    }
    public function venues()
{
    return $this->belongsToMany(Venue::class, 'film_venue', 'film_id', 'venue_id');
}
}
