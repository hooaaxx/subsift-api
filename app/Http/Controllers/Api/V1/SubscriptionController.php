<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\StoreSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(private readonly SubscriptionService $subscriptions) {}

    public function index(Request $request): JsonResponse
    {
        $items = $this->subscriptions->listForUser($request->user());

        return response()->json([
            'success' => true,
            'data'    => SubscriptionResource::collection($items),
            'message' => 'Subscriptions retrieved.',
        ]);
    }

    public function store(StoreSubscriptionRequest $request): JsonResponse
    {
        $subscription = $this->subscriptions->create($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'data'    => new SubscriptionResource($subscription),
            'message' => 'Subscription created.',
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('view', $subscription);

        return response()->json([
            'success' => true,
            'data'    => new SubscriptionResource($subscription),
            'message' => 'Subscription retrieved.',
        ]);
    }

    public function update(UpdateSubscriptionRequest $request, int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('update', $subscription);

        $updated = $this->subscriptions->update($subscription, $request->validated());

        return response()->json([
            'success' => true,
            'data'    => new SubscriptionResource($updated),
            'message' => 'Subscription updated.',
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $subscription = Subscription::findOrFail($id);
        $this->authorize('delete', $subscription);

        $this->subscriptions->delete($subscription);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Subscription deleted.',
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->subscriptions->summary($request->user()),
            'message' => 'Summary retrieved.',
        ], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function upcoming(Request $request): JsonResponse
    {
        $items = $this->subscriptions->upcoming($request->user());

        return response()->json([
            'success' => true,
            'data'    => SubscriptionResource::collection($items),
            'message' => 'Upcoming subscriptions retrieved.',
        ]);
    }
}
