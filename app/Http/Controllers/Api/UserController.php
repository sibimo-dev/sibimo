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
            ->latest('user_id')
            ->paginate(10);

        return response()->json([
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
            'password' => ['required', 'string', 'min:8'],
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
            'message' => 'User berhasil dibuat.',
            'data' => $user,
        ], 201);
    }

    /**
     * Display a user.
     */
    public function show(User $user): JsonResponse
    {
        return response()->json([
            'message' => 'Data user berhasil diambil.',
            'data' => $user,
        ]);
    }

    /**
     * Update a user.
     */
    public function update(Request $request, User $user): JsonResponse
    {
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
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'role' => ['sometimes', Rule::in(['Admin', 'Operator'])],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }
}