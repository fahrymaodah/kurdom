<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findByPhone(string $phone): ?User;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User;
}
