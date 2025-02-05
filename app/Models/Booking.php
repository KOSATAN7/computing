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
        'provider_id',
        'jumlah_orang',
        'total_harga',
        'bukti_pembayaran',
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

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'booking_menu', 'booking_id', 'menu_id')
            ->withPivot('jumlah_pesanan')
            ->withTimestamps();
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ProviderPembayaran::class, 'provider_id');
    }
}
