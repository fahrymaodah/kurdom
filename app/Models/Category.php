<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ── Scopes ────────────────────────────────

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
