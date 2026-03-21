<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CourierProfileRepositoryInterface;
use App\Repositories\Contracts\DeliveryFeeConfigRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\SellerProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\CourierProfileRepository;
use App\Repositories\DeliveryFeeConfigRepository;
use App\Repositories\OrderRepository;
use App\Repositories\SellerProfileRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DeliveryFeeConfigRepositoryInterface::class, DeliveryFeeConfigRepository::class);
        $this->app->bind(SellerProfileRepositoryInterface::class, SellerProfileRepository::class);
        $this->app->bind(CourierProfileRepositoryInterface::class, CourierProfileRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
