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
        return response()->json(['success' => true, 'data' => [], 'message' => '']);
    }

    public function ban(Request $request, int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => null, 'message' => '']);
    }

    public function unban(Request $request, int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => null, 'message' => '']);
    }

    public function maintenanceStatus(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['enabled' => false], 'message' => '']);
    }

    public function toggleMaintenance(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['enabled' => false], 'message' => '']);
    }
}
