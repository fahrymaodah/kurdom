<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_existing_user_by_phone(): void
    {
        $user = User::factory()->buyer()->create([
            'phone' => '08123456789',
            'name' => 'Found Buyer',
        ]);

        $found = User::where('phone', '08123456789')->first();

        $this->assertNotNull($found);
        $this->assertEquals('Found Buyer', $found->name);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_phone_not_found_returns_null(): void
    {
        $found = User::where('phone', '08000000000')->first();

        $this->assertNull($found);
    }

    public function test_phone_lookup_integrated_with_user_service(): void
    {
        $user = User::factory()->buyer()->create([
            'phone' => '08111222333',
            'name' => 'Buyer Dompu',
            'address_text' => 'Jl. Ahmad Yani, Dompu',
            'latitude' => -8.5365,
            'longitude' => 118.4633,
        ]);

        $service = app(\App\Services\UserService::class);
        $result = $service->findByPhone('08111222333');

        $this->assertNotNull($result);
        $this->assertEquals('Buyer Dompu', $result->name);
        $this->assertEquals('Jl. Ahmad Yani, Dompu', $result->address_text);
    }

    public function test_phone_lookup_service_not_found(): void
    {
        $service = app(\App\Services\UserService::class);
        $result = $service->findByPhone('08999999999');

        $this->assertNull($result);
    }

    public function test_phone_is_unique_across_users(): void
    {
        User::factory()->buyer()->create(['phone' => '08123456789']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::factory()->buyer()->create(['phone' => '08123456789']);
    }
}
