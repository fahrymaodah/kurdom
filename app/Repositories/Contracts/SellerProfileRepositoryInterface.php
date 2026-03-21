<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\SellerProfile;

interface SellerProfileRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function updateOrCreate(int $userId, array $data): SellerProfile;
}
