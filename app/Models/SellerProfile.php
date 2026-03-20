<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'store_description',
        'opening_time',
        'closing_time',
        'is_open',
        'average_rating',
    ];

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

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }
}
