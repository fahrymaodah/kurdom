<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class PhoneLookupService
{
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }
}
