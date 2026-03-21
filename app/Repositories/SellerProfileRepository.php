<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\SellerProfile;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;

class SellerProfileRepository implements SellerProfileRepositoryInterface
{
    public function updateOrCreate(int $userId, array $data): SellerProfile
    {
        return SellerProfile::updateOrCreate(
            ['user_id' => $userId],
            $data,
        );
    }
}
