<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function resellerProfile(): HasOne
    {
        return $this->hasOne(ResellerProfile::class);
    }

    public function customerProfile(): HasOne
    {
        return $this->hasOne(CustomerProfile::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function walletLedger(): HasMany
    {
        return $this->hasMany(WalletLedger::class);
    }

    public function generatedVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'generated_by');
    }

    public function soldVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'sold_by');
    }

    public function redeemedVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'redeemed_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isReseller(): bool
    {
        return $this->hasRole('reseller');
    }

    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    public function getWalletBalance(): float
    {
        return $this->resellerProfile?->wallet_balance ?? 0.0;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Gravatar fallback
        $hash = md5(strtolower(trim($this->email)));
        return "https://www.gravatar.com/avatar/{$hash}?d=identicon&s=80";
    }

    public function getDashboardRoute(): string
    {
        return match (true) {
            $this->isAdmin()    => 'admin.dashboard',
            $this->isReseller() => 'reseller.dashboard',
            default             => 'customer.dashboard',
        };
    }
}
