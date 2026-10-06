<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'wallet_balance',
        'commission_rate',
        'total_earned_commission',
        'is_approved',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'wallet_balance'          => 'decimal:3',
            'commission_rate'         => 'decimal:2',
            'total_earned_commission' => 'decimal:3',
            'is_approved'             => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
