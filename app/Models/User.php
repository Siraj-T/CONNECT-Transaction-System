<?php

namespace App\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWallet;
use Bavix\Wallet\Traits\HasWalletFloat;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Wallet, WalletFloat
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasWallet, HasWalletFloat;

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

    public function processedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'admin_id');
    }

    public function citizen(): HasOne
    {
        return $this->hasOne(Citizen::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
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
        return 'admin.dashboard';
    }
}
