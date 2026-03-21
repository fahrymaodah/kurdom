<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CourierProfile;
use App\Models\User;
use App\Repositories\Contracts\CourierProfileRepositoryInterface;

class CourierProfileService
{
    public function __construct(
        protected CourierProfileRepositoryInterface $courierProfileRepository,
    ) {}

    public function toggleOnline(User $user): CourierProfile
    {
        $currentStatus = $user->courierProfile?->is_online ?? false;

        return $this->courierProfileRepository->updateOrCreate($user->id, [
            'is_online' => ! $currentStatus,
        ]);
    }
}
