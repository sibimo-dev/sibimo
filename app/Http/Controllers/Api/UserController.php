<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()->with('roleRelation:role_id,name')
            ->select(['user_id', 'full_name', 'username', 'email', 'role_id', 'phone_number', 'is_active', 'created_at', 'updated_at'])
            ->orderByDesc('user_id')->get()->map(fn (User $user) => $this->userPayload($user));

        return response()->json(['success' => true, 'message' => 'Data user berhasil diambil.', 'data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['nullable', 'required_without:role', 'exists:roles,role_id'],
            'role' => ['nullable', 'required_without:role_id', Rule::in(['Superadmin', 'Admin', 'Operator'])],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $role = isset($validated['role_id'])
            ? Role::findOrFail($validated['role_id'])
            : Role::where('name', $validated['role'])->firstOrFail();
        $user = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            // Dipertahankan sementara untuk kompatibilitas data lama.
            'role' => $role->name,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil dibuat.', 'data' => $this->userPayload($user->load('roleRelation:role_id,name'))], 201);
    }

    public function show(int $user_id): JsonResponse
    {
        $user = User::query()->with('roleRelation:role_id,name')->findOrFail($user_id);
        return response()->json(['success' => true, 'message' => 'Detail pengguna berhasil diambil.', 'data' => $this->userPayload($user)]);
    }

    public function update(Request $request, int $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);
        $validated = $request->validate([
            'full_name' => ['sometimes', 'required', 'string', 'max:100'],
            'username' => ['sometimes', 'required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->user_id, 'user_id')],
            'email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->user_id, 'user_id')],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'role_id' => ['sometimes', 'required', 'exists:roles,role_id'],
            'role' => ['sometimes', 'required', Rule::in(['Superadmin', 'Admin', 'Operator'])],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);
        if (array_key_exists('password', $validated)) {
            if ($validated['password'] === null) unset($validated['password']);
            else $validated['password'] = Hash::make($validated['password']);
        }
        if (isset($validated['role_id'])) {
            $validated['role'] = Role::findOrFail($validated['role_id'])->name;
        } elseif (isset($validated['role'])) {
            $validated['role_id'] = Role::where('name', $validated['role'])->firstOrFail()->role_id;
        }
        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $this->userPayload($user->fresh()->load('roleRelation:role_id,name'))]);
    }

    public function destroy(Request $request, int $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);
        if ((int) $request->user()->user_id === $user->user_id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak dapat menghapus diri sendiri.'], 403);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'user_id' => $user->user_id, 'full_name' => $user->full_name, 'username' => $user->username,
            'email' => $user->email, 'role_id' => $user->role_id,
            'role' => $user->roleRelation ? ['role_id' => $user->roleRelation->role_id, 'name' => $user->roleRelation->name] : null,
            'phone_number' => $user->phone_number, 'is_active' => $user->is_active,
            'created_at' => $user->created_at, 'updated_at' => $user->updated_at,
        ];
    }
}
