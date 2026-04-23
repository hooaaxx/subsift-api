<?php

namespace App\Providers;

use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use App\Repositories\NotificationRepository;
use App\Repositories\SubscriptionRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SubscriptionRepositoryInterface::class, SubscriptionRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
    }
}
