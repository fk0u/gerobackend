<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Siti Rahma"),
     *             @OA\Property(property="email", type="string", format="email", example="siti@example.com"),
     *             @OA\Property(property="password", type="string", format="password", minLength=6, example="rahasia123"),
     *             @OA\Property(property="role", type="string", enum={"end_user", "mitra", "admin"}, example="end_user", description="Optional role override (defaults to end_user)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Siti Rahma"),
     *                     @OA\Property(property="email", type="string", example="siti@example.com"),
     *                     @OA\Property(property="role", type="string", example="end_user")
     *                 ),
     *                 @OA\Property(property="token", type="string", example="11|AbCdEf123456789....", description="Sanctum plain text token")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|in:end_user,mitra,admin',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:500',
            'vehicle_type' => 'sometimes|string|max:100',
            'vehicle_plate' => 'sometimes|string|max:20',
            'work_area' => 'sometimes|string|max:255',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'end_user',
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'vehicle_plate' => $data['vehicle_plate'] ?? null,
            'work_area' => $data['work_area'] ?? null,
            'status' => 'active',
            'points' => 0,
            'total_collections' => 0,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', ' =>', $credentials['email'], 'and')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens (optional security hardening)
        $user->tokens()->delete();
        $token = $user->createToken('mobile')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get current user profile",
     *     tags={"Authentication"},
     *     security={{"SanctumToken": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User data retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="User Daffa"),
     *                     @OA\Property(property="email", type="string", example="daffa@gmail.com"),
     *                     @OA\Property(property="role", type="string", example="end_user"),
     *                     @OA\Property(property="profile_picture", type="string", example="assets/img_friend1.png"),
     *                     @OA\Property(property="phone", type="string", example="081234567890"),
     *                     @OA\Property(property="address", type="string", example="Jl. Merdeka No. 1, Jakarta"),
     *                     @OA\Property(property="subscription_status", type="string", example="active"),
     *                     @OA\Property(property="points", type="integer", example=50),
     *                     @OA\Property(property="status", type="string", example="active"),
     *                     @OA\Property(property="total_collections", type="integer", example=0),
     *                     @OA\Property(property="created_at", type="string", example="2025-10-06 05:36:59"),
     *                     @OA\Property(property="updated_at", type="string", example="2025-10-06 05:43:41")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(Request $request)
    {
        return $this->successResponse([
            'user' => new UserResource($request->user())
        ], 'User data retrieved successfully');
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout current user",
     *     tags={"Authentication"},
     *     security={{"SanctumToken": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Logged out successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $current = $user?->currentAccessToken();
        if ($user && $current) {
            $user->tokens()->where('id', $current->id)->delete();
        }
        
        return $this->successResponse(null, 'Logged out successfully');
    }
}
