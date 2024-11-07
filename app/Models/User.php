<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['username', 'password', 'email', 'role'];

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'created_by');
    }
}