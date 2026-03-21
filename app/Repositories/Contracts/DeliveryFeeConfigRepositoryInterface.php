<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\DeliveryFeeConfig;

interface DeliveryFeeConfigRepositoryInterface
{
    public function getActiveConfig(): ?DeliveryFeeConfig;
}
