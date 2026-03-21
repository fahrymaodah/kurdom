<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '08000000001'],
            [
                'name' => 'Admin KurDom',
                'email' => 'admin@kurdom.test',
                'password' => bcrypt('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
            ],
        );

        User::updateOrCreate(
            ['phone' => '08000000002'],
            [
                'name' => 'Toko Demo',
                'email' => 'seller@kurdom.test',
                'password' => bcrypt('password'),
                'role' => UserRole::Seller,
                'is_active' => true,
                'address_text' => 'Jl. Soekarno Hatta, Dompu',
                'latitude' => -8.5370,
                'longitude' => 118.4631,
            ],
        );

        User::updateOrCreate(
            ['phone' => '08000000003'],
            [
                'name' => 'Kurir Demo',
                'email' => 'courier@kurdom.test',
                'password' => bcrypt('password'),
                'role' => UserRole::Courier,
                'is_active' => true,
                'address_text' => 'Jl. Ahmad Yani, Dompu',
                'latitude' => -8.5340,
                'longitude' => 118.4670,
            ],
        );
    }
}
