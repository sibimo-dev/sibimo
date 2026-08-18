<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display all users.
     */
    public function index(): JsonResponse
    {
        $users = User::query()
            ->select([
                'user_id',
                'full_name',
                'username',
                'email',
                'role',
                'phone_number',
                'is_active',
                'created_at',
                'updated_at',
            ])
            ->orderBy('user_id', 'desc')
            ->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data user berhasil diambil.',
            'data' => $users,
        ]);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['Admin', 'Operator'])],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'succes' => true,
            'message' => 'User berhasil dibuat.',
            'data' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'phone_number' => $user->phone_number,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]
        ], 201);
    }

    /**
     * Display a user.
     */
    public function show(int $user_id): JsonResponse
    {
        $user = User::query()
            ->select([
                'user_id',
                'full_name',
                'username',
                'email',
                'role',
                'phone_number',
                'is_active',
                'created_at',
                'updated_at',
            ])
            ->findOrFail($user_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail pengguna berhasil diambil.',
            'data' => $user,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, int $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);

        if (!$user) {
            return response()->json([
                'succes' => false,
                'message' => 'User tidak ditemukan.',
            ], 404);
        }

        $primaryKey = $user->user_id ?? $user->id;

        $validated = $request->validate([
            'full_name' => ['sometimes', 'required', 'string', 'max:100'],
            'username' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')
                    ->ignore($user->user_id, 'user_id'),
            ],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:100',
                Rule::unique('users', 'email')
                    ->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'role' => ['sometimes', 'required', Rule::in(['Admin', 'Operator'])],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);

        if (array_key_exists('password', $validated)) {
            if ($validated['password'] !== null) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }
        }

        $user->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'User berhasil diperbarui.',
            'data' => [
                'user_id' => $user->user_id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
                'role' => $user->role,
                'phone_number' => $user->phone_number,
                'is_active' => $user->is_active,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, int $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);

        if ((int) $request->user()->user_id === $user->user_id) {
            return response()->json([
                'succes' => false,
                'message' => 'Anda tidak dapat menghapus diri sendiri.',
            ], 403);
        }

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'succes' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }
}