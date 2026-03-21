<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\DeliveryFeeConfig;
use App\Repositories\Contracts\DeliveryFeeConfigRepositoryInterface;

class DeliveryFeeConfigRepository implements DeliveryFeeConfigRepositoryInterface
{
    public function getActiveConfig(): ?DeliveryFeeConfig
    {
        return DeliveryFeeConfig::active()->latest()->first();
    }
}
