<?php

namespace App\Repositories;

use App\Models\Subscription;
use App\Models\User;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function allForUser(User $user): Collection
    {
        return $user->subscriptions()->orderBy('next_billing_date')->get();
    }

    public function findForUser(int $id, User $user): Subscription
    {
        return $user->subscriptions()->findOrFail($id);
    }

    public function create(array $data): Subscription
    {
        return Subscription::create($data);
    }

    public function update(Subscription $subscription, array $data): Subscription
    {
        $subscription->update($data);
        return $subscription->fresh();
    }

    public function delete(Subscription $subscription): void
    {
        $subscription->delete();
    }

    public function upcomingForUser(User $user): Collection
    {
        return $user->subscriptions()
            ->whereBetween('next_billing_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->orderBy('next_billing_date')
            ->get();
    }

    public function dueForAlert(): Collection
    {
        $from = now()->addHours(48)->startOfDay();
        $to   = now()->addHours(72)->endOfDay();

        return Subscription::where('alert_enabled', true)
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('next_billing_date', [$from, $to])
                  ->orWhere(function ($q2) use ($from, $to) {
                      $q2->where('is_trial', true)
                         ->whereBetween('trial_ends_at', [$from, $to]);
                  });
            })
            ->with('user')
            ->get();
    }
}
