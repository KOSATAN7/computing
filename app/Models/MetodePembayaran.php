<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MetodePembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'aktif'

    ];

    public function providers()
    {
        return $this->hasMany(ProviderPembayaran::class, 'metode_pembayaran_id');
    }
}
