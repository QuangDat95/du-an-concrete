<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\DebtDetail\DebtDetailRepository;
use App\Repositories\PaymentItem\PaymentItemRepository;
use App\Repositories\Customer\CustomerRepositoryInterface;
use App\Repositories\VolumeTracking\VolumeTrackingRepository;
use App\Repositories\DebtDetail\DebtDetailRepositoryInterface;
use App\Repositories\PaymentItem\PaymentItemRepositoryInterface;
use App\Repositories\VolumeTracking\VolumeTrackingRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            VolumeTrackingRepositoryInterface::class, VolumeTrackingRepository::class
        );
        $this->app->bind(
            CustomerRepositoryInterface::class, CustomerRepository::class
        );
        $this->app->bind(
            PaymentItemRepositoryInterface::class, PaymentItemRepository::class
        );
        $this->app->bind(
            DebtDetailRepositoryInterface::class, DebtDetailRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
