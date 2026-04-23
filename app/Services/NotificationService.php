<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $notifications
    ) {}

    public function listForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $this->notifications->paginatedForUser($user, $perPage);
    }

    public function unreadCount(User $user): int
    {
        return $this->notifications->unreadCountForUser($user);
    }

    public function markAsRead(Notification $notification): Notification
    {
        return $this->notifications->markAsRead($notification);
    }

    public function markAllRead(User $user): void
    {
        $this->notifications->markAllReadForUser($user);
    }
}
