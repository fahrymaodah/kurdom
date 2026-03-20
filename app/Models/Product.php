<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'description',
        'price',
        'photo',
        'is_available',
        'is_preorder',
        'min_preorder_hours',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_preorder' => 'boolean',
            'min_preorder_hours' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ── Scopes ────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopePreorder($query)
    {
        return $query->where('is_preorder', true);
    }
}
