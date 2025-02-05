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
    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_menu', 'menu_id', 'booking_id')->withTimestamps();
    }
}
