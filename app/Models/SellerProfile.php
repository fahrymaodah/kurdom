<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'store_name', 'store_description',
    'opening_time', 'closing_time', 'is_open', 'average_rating',
])]
class SellerProfile extends Model
{

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'average_rating' => 'decimal:2',
            'opening_time' => 'datetime:H:i',
            'closing_time' => 'datetime:H:i',
        ];
    }

    // ── Relationships ─────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('is_open', true);
    }
}
