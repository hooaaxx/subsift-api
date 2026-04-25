<?php

namespace App\Services;

use App\Actions\CreateSubscriptionAction;
use App\Actions\UpdateSubscriptionAction;
use App\DTOs\SubscriptionData;
use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionService
{
    public function __construct(
        private readonly SubscriptionRepositoryInterface $subscriptions,
        private readonly CreateSubscriptionAction        $createAction,
        private readonly UpdateSubscriptionAction        $updateAction,
    ) {}

    public function listForUser(User $user): Collection
    {
        return $this->subscriptions->allForUser($user);
    }

    public function create(array $validated, User $user): Subscription
    {
        $data = SubscriptionData::fromRequest($validated, $user->id);
        return $this->createAction->execute($data);
    }

    public function find(int $id, User $user): Subscription
    {
        return $this->subscriptions->findForUser($id, $user);
    }

    public function update(Subscription $subscription, array $validated): Subscription
    {
        $data = SubscriptionData::fromRequest($validated, $subscription->user_id);
        return $this->updateAction->execute($subscription, $data);
    }

    public function delete(Subscription $subscription): void
    {
        $this->subscriptions->delete($subscription);
    }

    public function summary(User $user): array
    {
        $subscriptions = $this->subscriptions->allForUser($user);

        $active    = $subscriptions->where('status', 'active');
        $cancelled = $subscriptions->where('status', 'cancelled');

        $monthlyTotal = $active->where('billing_cycle', 'monthly')->sum('cost');
        $annualTotal  = $active->where('billing_cycle', 'yearly')->sum('cost')
                      + ($monthlyTotal * 12);

        $cancelledMonthlySavings = $cancelled->where('billing_cycle', 'monthly')->sum('cost')
                                 + ($cancelled->where('billing_cycle', 'yearly')->sum('cost') / 12);

        return [
            'monthly_total'             => (float) round($monthlyTotal, 2),
            'annual_total'              => (float) round($annualTotal, 2),
            'cancelled_monthly_savings' => (float) round($cancelledMonthlySavings, 2),
            'cancelled_count'           => $cancelled->count(),
        ];
    }

    public function upcoming(User $user): Collection
    {
        return $this->subscriptions->upcomingForUser($user);
    }
}
