<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Citizen extends Model
{
    protected $fillable = ['name', 'phone_number'];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
