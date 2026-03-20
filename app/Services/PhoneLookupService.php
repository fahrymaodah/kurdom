<?php

namespace App\Services;

use App\Models\User;

class PhoneLookupService
{
    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }
}
