<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
    ) {}

    public function findByPhone(string $phone): ?User
    {
        return $this->userRepository->findByPhone($phone);
    }

    public function toggleActive(User $user): User
    {
        return $this->userRepository->update($user, [
            'is_active' => ! $user->is_active,
        ]);
    }
}
