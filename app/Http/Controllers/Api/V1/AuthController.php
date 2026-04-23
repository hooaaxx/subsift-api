<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'success' => true,
            'data'    => [
                'user'         => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type'   => 'Bearer',
            ],
            'message' => 'Registration successful.',
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->only('email', 'password'));

        return response()->json([
            'success' => true,
            'data'    => [
                'user'         => new UserResource($result['user']),
                'access_token' => $result['token'],
                'token_type'   => 'Bearer',
            ],
            'message' => 'Login successful.',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Logged out successfully.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource($request->user()),
            'message' => 'Authenticated user retrieved.',
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $this->authService->sendPasswordResetLink($request->email);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Password reset link sent.',
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $this->authService->resetPassword($request->validated());

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'Password reset successfully.',
        ]);
    }
}
