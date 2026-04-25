<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\RenewalVerificationController;
use App\Http\Controllers\Api\V1\SubscriptionController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public
    Route::get('renewal/verify/{token}', [RenewalVerificationController::class, 'verify']);
    Route::post('renewal/price/{token}', [RenewalVerificationController::class, 'updatePrice']);

    Route::get('status', function () {
        return response()->json([
            'success' => true,
            'data'    => ['maintenance' => Cache::get('maintenance_mode', false)],
            'message' => 'Status retrieved.',
        ]);
    });

    // Auth (public)
    Route::prefix('auth')->group(function () {
        Route::post('register',        [AuthController::class, 'register']);
        Route::post('login',           [AuthController::class, 'login']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password',  [AuthController::class, 'resetPassword']);
    });

    // me + logout bypass banned/maintenance so banned users can read their status and log out
    Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me',      [AuthController::class, 'me']);
    });

    // Protected (banned and maintenance checks apply)
    Route::middleware(['auth:sanctum', 'not-banned', 'not-maintenance'])->group(function () {

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

    // Admin
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        Route::get('users',             [AdminController::class, 'users']);
        Route::post('users/{id}/ban',   [AdminController::class, 'ban']);
        Route::post('users/{id}/unban', [AdminController::class, 'unban']);
        Route::get('maintenance',       [AdminController::class, 'maintenanceStatus']);
        Route::post('maintenance',      [AdminController::class, 'toggleMaintenance']);
    });
});
