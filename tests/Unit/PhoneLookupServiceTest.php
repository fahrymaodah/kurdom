<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Mockery;
use Tests\TestCase;

class PhoneLookupServiceTest extends TestCase
{
    private UserService $service;

    private UserRepositoryInterface $userRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepo = Mockery::mock(UserRepositoryInterface::class);
        $this->service = new UserService($this->userRepo);
    }

    public function test_find_by_phone_returns_user_when_found(): void
    {
        $user = new User;
        $user->name = 'Test Buyer';
        $user->phone = '08123456789';

        $this->userRepo->shouldReceive('findByPhone')
            ->with('08123456789')
            ->once()
            ->andReturn($user);

        $result = $this->service->findByPhone('08123456789');

        $this->assertNotNull($result);
        $this->assertEquals('Test Buyer', $result->name);
        $this->assertEquals('08123456789', $result->phone);
    }

    public function test_find_by_phone_returns_null_when_not_found(): void
    {
        $this->userRepo->shouldReceive('findByPhone')
            ->with('08000000000')
            ->once()
            ->andReturn(null);

        $result = $this->service->findByPhone('08000000000');

        $this->assertNull($result);
    }
}
