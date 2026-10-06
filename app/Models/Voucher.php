<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_plan_id',
        'code',
        'batch_id',
        'status',
        'generated_by',
        'sold_by',
        'sold_to',
        'redeemed_by',
        'sell_price_lyd',
        'sold_at',
        'redeemed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'sell_price_lyd' => 'decimal:3',
            'sold_at'        => 'datetime',
            'redeemed_at'    => 'datetime',
            'expires_at'     => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(VoucherPlan::class, 'voucher_plan_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_to');
    }

    public function redeemer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by');
    }
}
