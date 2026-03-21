<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CourierProfile;
use App\Repositories\Contracts\CourierProfileRepositoryInterface;

class CourierProfileRepository implements CourierProfileRepositoryInterface
{
    public function updateOrCreate(int $userId, array $data): CourierProfile
    {
        return CourierProfile::updateOrCreate(
            ['user_id' => $userId],
            $data,
        );
    }
}
