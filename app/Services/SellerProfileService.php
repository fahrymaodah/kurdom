<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SellerProfile;
use App\Models\User;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;

class SellerProfileService
{
    public function __construct(
        protected SellerProfileRepositoryInterface $sellerProfileRepository,
        protected UserRepositoryInterface $userRepository,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): SellerProfile
    {
        $profile = $this->sellerProfileRepository->updateOrCreate($user->id, [
            'store_name' => $data['store_name'],
            'store_description' => $data['store_description'],
            'opening_time' => $data['opening_time'],
            'closing_time' => $data['closing_time'],
            'is_open' => $data['is_open'],
        ]);

        if (isset($data['address_text'])) {
            $this->userRepository->update($user, [
                'address_text' => $data['address_text'],
            ]);
        }

        return $profile;
    }
}
