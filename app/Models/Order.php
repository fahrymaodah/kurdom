<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'order_code', 'order_source', 'seller_id', 'courier_id', 'buyer_id',
    'buyer_name', 'buyer_phone',
    'pickup_latitude', 'pickup_longitude', 'pickup_address_text',
    'delivery_latitude', 'delivery_longitude', 'delivery_address_text',
    'distance_km', 'item_price', 'delivery_fee', 'total',
    'notes', 'status',
    'courier_assigned_at', 'picked_up_at', 'delivery_started_at',
    'completed_at', 'cancelled_at', 'cancel_reason',
])]
class Order extends Model
{

    protected function casts(): array
    {
        return [
            'order_source' => OrderSource::class,
            'status' => OrderStatus::class,
            'pickup_latitude' => 'decimal:7',
            'pickup_longitude' => 'decimal:7',
            'delivery_latitude' => 'decimal:7',
            'delivery_longitude' => 'decimal:7',
            'distance_km' => 'decimal:2',
            'item_price' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'courier_assigned_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivery_started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function courier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'courier_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    // ── Scopes ────────────────────────────────

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::New);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', [
            OrderStatus::CourierAssigned,
            OrderStatus::PickedUp,
            OrderStatus::InDelivery,
        ]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Completed);
    }

    public function scopeFromSource(Builder $query, OrderSource $source): Builder
    {
        return $query->where('order_source', $source);
    }

    // ── State Machine ─────────────────────────

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }
}
