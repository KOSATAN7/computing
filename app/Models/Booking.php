<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'venue_id',
        'jumlah_orang',
        'bukti_pembayaran',
        'provider_id', 
        'status'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'booking_menu', 'booking_id', 'menu_id')->withTimestamps();
    }

    public function provider()
{
    return $this->belongsTo(ProviderPembayaran::class, 'provider_id');
}

}
