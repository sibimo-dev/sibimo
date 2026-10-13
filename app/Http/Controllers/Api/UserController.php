<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'role_id' => ['required', 'exists:roles,role_id'],
            'phone_number' => ['nullable', 'string', 'max:15'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $role = Role::findOrFail($validated['role_id']);
        $user = User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role_id' => $role->role_id,
            'phone_number' => $validated['phone_number'] ?? null,
            'password' => Hash::make($validated['password']),
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
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:15'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ]);
        $isDeactivating = array_key_exists('is_active', $validated)
            && $validated['is_active'] === false;

        if ($isDeactivating && ($this->isProtectedUser($user) || (int) $request->user()->user_id === $user->user_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Superadmin dan akun yang sedang digunakan tidak dapat dinonaktifkan.',
            ], 403);
        }

        if (array_key_exists('password', $validated)) {
            if ($validated['password'] === null) unset($validated['password']);
            else $validated['password'] = Hash::make($validated['password']);
        }

        if (isset($validated['role_id'])) {
            $validated['role'] = Role::findOrFail($validated['role_id'])->name;
        }
        $user->update($validated);

        if ($isDeactivating) {
            $user->tokens()->delete();
        }

        return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.', 'data' => $this->userPayload($user->fresh()->load('roleRelation:role_id,name'))]);
    }

    public function destroy(Request $request, int $user_id): JsonResponse
    {
        $user = User::findOrFail($user_id);
        if ((int) $request->user()->user_id === $user->user_id || $this->isProtectedUser($user)) {
            return response()->json(['success' => false, 'message' => 'Superadmin dan akun yang sedang digunakan tidak dapat dihapus.'], 403);
        }
        $user->tokens()->delete();
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,user_id'],
            'action' => ['required', Rule::in(['delete', 'deactivate', 'activate'])],
        ]);

        $actorId = (int) $request->user()->user_id;
        $processed = [];
        $skipped = [];

        DB::transaction(function () use ($validated, $actorId, &$processed, &$skipped): void {
            $users = User::query()
                ->with('roleRelation:role_id,name')
                ->whereIn('user_id', $validated['user_ids'])
                ->lockForUpdate()
                ->get()
                ->keyBy('user_id');

            foreach ($validated['user_ids'] as $userId) {
                $user = $users->get($userId);

                if ($userId === $actorId) {
                    $skipped[] = ['user_id' => $userId, 'reason' => 'Akun yang sedang digunakan tidak dapat diproses.'];
                    continue;
                }

                if ($this->isProtectedUser($user)) {
                    $skipped[] = ['user_id' => $userId, 'reason' => 'Akun Superadmin tidak dapat diproses.'];
                    continue;
                }

                if ($validated['action'] === 'delete') {
                    $user->tokens()->delete();
                    $user->delete();
                } elseif ($validated['action'] === 'deactivate') {
                    $user->update(['is_active' => false]);
                    $user->tokens()->delete();
                } else {
                    $user->update(['is_active' => true]);
                }

                $processed[] = $userId;
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Aksi massal pengguna berhasil diproses.',
            'data' => [
                'action' => $validated['action'],
                'processed_ids' => $processed,
                'processed_count' => count($processed),
                'skipped' => $skipped,
                'skipped_count' => count($skipped),
            ],
        ]);
    }

    private function isProtectedUser(?User $user): bool
    {
        return $user?->roleRelation?->name === 'Superadmin';
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
