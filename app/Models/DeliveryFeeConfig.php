<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'distance_threshold_km', 'near_rate', 'far_rate',
    'night_start_time', 'night_end_time', 'night_surcharge', 'is_active',
])]
class DeliveryFeeConfig extends Model
{

    protected function casts(): array
    {
        return [
            'distance_threshold_km' => 'decimal:2',
            'near_rate' => 'decimal:2',
            'far_rate' => 'decimal:2',
            'night_surcharge' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
