<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Login user.
     *
     * Bisa login menggunakan username atau email.
     */
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $validated['login'])
            ->orWhere('email', $validated['login'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['Username/email atau password salah.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun kamu tidak aktif.',
            ], 403);
        }

        // Hapus token lama jika ingin satu sesi/token aktif.
        // Kalau ingin banyak device bisa login, baris ini boleh dihapus.
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'is_active' => $user->is_active,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Mendapatkan data user yang sedang login.
     */
    public function me(Request $request): JsonResponse
    {
        $bearer = $request->bearerToken();

        $token = PersonalAccessToken::findToken($bearer);
    
        if (!$token) {
            return response()->json([
                'message' => 'Sesi Anda telah berakhir atau token tidak valid.',
            ], 401);
        }

        $user = $token->tokenable;

        if ($user->is_active === false || $user->is_active === '0') {
            return response()->json([
                'message' => 'Akun kamu tidak aktif.',
            ], 403);
        }

        return response()->json([
            'message' => 'Data user berhasil diambil.',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone_number' => $user->phone_number,
                    'is_active' => $user->is_active,
                ]
            ],
        ]);
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'message' => 'Token tidak ditemukan.'
            ], 401);
        }

        $token = PersonalAccessToken::findToken($bearer);

        if (!$token) {
            return response()->json([
                'message' => 'Token tidak valid atau sudah kadaluarsa.'
            ], 401);
        }

        $token->delete();

        return response()->json([
            'success' => 'success',
            'message' => 'Berhasil Logout dan token telah dihapus.',
        ]);
    }
}