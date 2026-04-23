<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Auth (public)
    Route::prefix('auth')->group(function () {
        Route::post('register',        [AuthController::class, 'register']);
        Route::post('login',           [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password',  [AuthController::class, 'resetPassword']);
    });

    // Protected
    Route::middleware(['auth:sanctum', 'not-banned', 'not-maintenance'])->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',      [AuthController::class, 'me']);
        });

        // Subscriptions
        Route::get('subscriptions/summary',  [SubscriptionController::class, 'summary']);
        Route::get('subscriptions/upcoming', [SubscriptionController::class, 'upcoming']);
        Route::apiResource('subscriptions',  SubscriptionController::class);

        // Notifications
        Route::get('notifications/unread-count',  [NotificationController::class, 'unreadCount']);
        Route::patch('notifications/read-all',    [NotificationController::class, 'markAllRead']);
        Route::get('notifications',               [NotificationController::class, 'index']);
        Route::patch('notifications/{id}/read',   [NotificationController::class, 'markAsRead']);
    });
});
