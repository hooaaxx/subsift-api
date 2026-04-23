<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class AdminController extends Controller
{
    public function users(Request $request): JsonResponse
    {
        $users = User::where('id', '!=', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => UserResource::collection($users),
            'message' => 'Users retrieved.',
        ]);
    }

    public function ban(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        abort_if($user->id === $request->user()->id, 403, 'Cannot ban yourself.');

        $user->update(['banned_at' => now()]);

        PersonalAccessToken::where('tokenable_id', $user->id)->delete();
        DB::table('sessions')->where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user->fresh()),
            'message' => 'User banned and logged out.',
        ]);
    }

    public function unban(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update(['banned_at' => null]);

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user->fresh()),
            'message' => 'User unbanned.',
        ]);
    }

    public function maintenanceStatus(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => ['enabled' => Cache::get('maintenance_mode', false)],
            'message' => 'Maintenance status retrieved.',
        ]);
    }

    public function toggleMaintenance(Request $request): JsonResponse
    {
        $enabled = $request->boolean('enabled');
        Cache::put('maintenance_mode', $enabled);

        return response()->json([
            'success' => true,
            'data'    => ['enabled' => $enabled],
            'message' => $enabled ? 'Maintenance mode enabled.' : 'Maintenance mode disabled.',
        ]);
    }
}
