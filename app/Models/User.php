<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'phone', 'email', 'password', 'role', 'profile_photo', 'address_text', 'latitude', 'longitude', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->role === UserRole::Admin && $this->is_active,
            'seller' => $this->role === UserRole::Seller && $this->is_active,
            'courier' => $this->role === UserRole::Courier && $this->is_active,
            default => false,
        };
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────

    public function courierProfile(): HasOne
    {
        return $this->hasOne(CourierProfile::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function ordersAsCourier(): HasMany
    {
        return $this->hasMany(Order::class, 'courier_id');
    }

    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function ratingsGiven(): HasMany
    {
        return $this->hasMany(Rating::class, 'from_user_id');
    }

    public function ratingsReceived(): HasMany
    {
        return $this->hasMany(Rating::class, 'to_user_id');
    }

    // ── Scopes ────────────────────────────────

    public function scopeRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ───────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::Seller;
    }

    public function isCourier(): bool
    {
        return $this->role === UserRole::Courier;
    }

    public function isBuyer(): bool
    {
        return $this->role === UserRole::Buyer;
    }
}
