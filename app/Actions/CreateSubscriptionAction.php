<?php

namespace App\Actions;

use App\DTOs\SubscriptionData;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class CreateSubscriptionAction
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions
    ) {}

    public function execute(SubscriptionData $data): Subscription
    {
        return $this->subscriptions->create($data->toArray());
    }
}
