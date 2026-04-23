<?php

namespace App\Actions;

use App\DTOs\SubscriptionData;
use App\Models\Subscription;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;

class UpdateSubscriptionAction
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions
    ) {}

    public function execute(Subscription $subscription, SubscriptionData $data): Subscription
    {
        return $this->subscriptions->update($subscription, $data->toArray());
    }
}
