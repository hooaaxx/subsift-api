<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function index(Request $request): JsonResponse
    {
        $paginated = $this->notifications->listForUser($request->user());

        return response()->json([
            'success' => true,
            'data'    => NotificationResource::collection($paginated->items()),
            'message' => 'Notifications retrieved.',
            'meta'    => [
                'pagination' => [
                    'total'        => $paginated->total(),
                    'per_page'     => $paginated->perPage(),
                    'current_page' => $paginated->currentPage(),
                    'last_page'    => $paginated->lastPage(),
                ],
            ],
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => ['count' => $this->notifications->unreadCount($request->user())],
            'message' => 'Unread count retrieved.',
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::where('user_id', $request->user()->id)->findOrFail($id);
        $updated = $this->notifications->markAsRead($notification);

        return response()->json([
            'success' => true,
            'data'    => new NotificationResource($updated),
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $this->notifications->markAllRead($request->user());

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'All notifications marked as read.',
        ]);
    }
}
