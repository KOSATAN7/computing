<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Venue extends Model
{
    use HasFactory;

    protected $table = 'venues';

    protected $fillable = [
        'admin_id',
        'pertandingan_id',
        'provider_id',
        'nama',
        'alamat',
        'kapasitas',
        'fasilitas',
        'kota',
        'latitude',
        'longitude',
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

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function provider()
    {
        return $this->hasOne(ProviderPembayaran::class, 'venue_id');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'venue_id', 'user_id')->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
