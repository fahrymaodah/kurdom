<?php

namespace App\Models;

use App\Enums\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierProfile extends Model
{
    protected $fillable = [
        'user_id',
        'is_online',
        'zone_latitude',
        'zone_longitude',
        'zone_radius_km',
        'vehicle',
        'license_plate',
        'average_rating',
    ];

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'zone_latitude' => 'decimal:7',
            'zone_longitude' => 'decimal:7',
            'zone_radius_km' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'vehicle' => Vehicle::class,
        ];
    }

    // ── Relationships ─────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->online()->whereHas('user', fn ($q) => $q->active());
    }
}
