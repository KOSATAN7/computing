<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = ['venue_id', 'nama', 'deskripsi', 'harga', 'foto', 'kategori'];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }
}
