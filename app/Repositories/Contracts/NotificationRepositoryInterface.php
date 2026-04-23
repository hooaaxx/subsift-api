<?php

namespace App\Repositories\Contracts;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface
{
    public function paginatedForUser(User $user, int $perPage = 15): LengthAwarePaginator;

    public function unreadCountForUser(User $user): int;

    public function create(array $data): Notification;

    public function markAsRead(Notification $notification): Notification;

    public function markAllReadForUser(User $user): void;
}
