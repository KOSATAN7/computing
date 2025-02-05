<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasFactory;

    protected $fillable = ['username', 'password', 'email', 'role'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function venues()
    {
        return $this->hasMany(Venue::class, 'created_by');
    }

    public function managedVenues()
    {
        return $this->hasMany(Venue::class, 'admin_id');
    }

    public function favoriteVenues(): BelongsToMany
    {
        return $this->belongsToMany(Venue::class, 'user_favorites', 'user_id', 'venue_id')->withTimestamps();
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
