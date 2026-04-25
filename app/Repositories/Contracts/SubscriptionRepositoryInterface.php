<?php

namespace App\Repositories\Contracts;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface SubscriptionRepositoryInterface
{
    public function allForUser(User $user): Collection;

    public function findForUser(int $id, User $user): Subscription;

    public function create(array $data): Subscription;

    public function update(Subscription $subscription, array $data): Subscription;

    public function delete(Subscription $subscription): void;

    public function upcomingForUser(User $user): Collection;

    public function dueForAlert(): Collection;

    public function dueForVerification(): Collection;
}
