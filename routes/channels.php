<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('seller.{id}', function (User $user, int $id): bool {
    return $user->id === $id;
});

Broadcast::channel('courier.{id}', function (User $user, int $id): bool {
    return $user->id === $id;
});

Broadcast::channel('buyer.{id}', function (User $user, int $id): bool {
    return $user->id === $id;
});
