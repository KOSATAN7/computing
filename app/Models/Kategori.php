<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';
    protected $fillable = [
        'nama'
    ];

    public function films()
    {
        return $this->hasMany(Films::class, 'kategori','id'); // 'kategori' adalah foreign key
    }
    
}
