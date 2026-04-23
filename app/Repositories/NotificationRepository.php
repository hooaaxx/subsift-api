<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function paginatedForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()
            ->where('channel', 'in_app')
            ->latest()
            ->paginate($perPage);
    }

    public function unreadCountForUser(User $user): int
    {
        return $user->notifications()
            ->where('channel', 'in_app')
            ->whereNull('read_at')
            ->count();
    }

    public function create(array $data): Notification
    {
        return Notification::create($data);
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['read_at' => now()]);
        return $notification->fresh();
    }

    public function markAllReadForUser(User $user): void
    {
        $user->notifications()
            ->where('channel', 'in_app')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
