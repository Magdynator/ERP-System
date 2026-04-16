<?php

declare(strict_types=1);

namespace Erp\Core\Http\Controllers\Api;

use Erp\Core\Http\Controllers\Controller;
use Erp\Core\Http\Requests\LoginRequest;
use Erp\Core\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="Authenticate user and return Sanctum token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@erp.test"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="1|abcdef123456..."),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {

        $result = $this->authService->apiLogin($request->email, $request->password, $request->device_name);

        if (! $result) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return response()->json($result);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Revoke current user token",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=204, description="Logout successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->apiLogout();

        return response()->json(null, 204);
    }
}
