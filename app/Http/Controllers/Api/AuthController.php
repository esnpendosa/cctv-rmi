<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Class AuthController
 * 
 * Handles Sanctum API authentication for mobile client.
 * 
 * @package App\Http\Controllers\Api
 */
class AuthController extends Controller
{
    use ApiResponder;

    /**
     * Authenticate user and return Bearer token.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email atau password salah.', 401, [
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.']
            ]);
        }

        $deviceName = $request->input('device_name', 'Flutter Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Login berhasil.');
    }

    /**
     * Revoke the user's current token.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return $this->successResponse(null, 'Logout berhasil.');
    }

    /**
     * Return the authenticated user profile.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        return $this->successResponse(new UserResource($request->user()), 'Data user berhasil diambil.');
    }
}
