<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'data_limit_gb',
        'duration_days',
        'price_lyd',
        'reseller_price_lyd',
        'retail_price_lyd',
        'speed_mbps',
        'is_active',
        'color_hex',
        'sort_order',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'data_limit_gb'      => 'decimal:2',
            'price_lyd'          => 'decimal:3',
            'reseller_price_lyd' => 'decimal:3',
            'retail_price_lyd'   => 'decimal:3',
            'is_active'          => 'boolean',
        ];
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
