<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $table = 'venues';
    protected $fillable = [
        'admin_id',
        'nama',
        'alamat',
        'kapasitas',
        'fasilitas',
        'kota',
        'foto',
        'video',
        'kontak',
        'status',
    ];

    protected $casts = [
        'fasilitas' => 'array', 
    ];


    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    // public function menus()
    // {
    //     return $this->hasMany(Menu::class, 'venue_id');
    // }

    public function films()
    {
        return $this->belongsToMany(Films::class, 'film_venue', 'venue_id', 'film_id');
    }

    // public function reviews()
    // {
    //     return $this->hasMany(Review::class, 'venue_id');
    // }
}
