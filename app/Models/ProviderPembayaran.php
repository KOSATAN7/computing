<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderPembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'metode_pembayaran_id',
        'venue_id',
        'nama',
        'no_rek',
        'penerima',
        'deskripsi',
        'foto',
        'aktif'
    ];

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'metode_pembayaran_id');
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
