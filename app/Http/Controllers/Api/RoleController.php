<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * GET /api/roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar role berhasil diambil',
            'data' => $roles,
        ]);
    }

    /**
     * GET /api/roles/{id}
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail role berhasil diambil',
            'data' => $role,
        ]);
    }

    /**
     * POST /api/roles
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dibuat',
            'data' => $role->load('permissions'),
        ], 201);
    }

    /**
     * PUT/PATCH /api/roles/{id}
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('roles', 'name')->ignore($role->role_id, 'role_id'),
            ],
            'description' => 'nullable|string',
        ]);

        if (($validated['name'] ?? $role->name) !== $role->name && $role->name === 'Superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Role Superadmin tidak dapat diganti nama.',
            ], 403);
        }

        $oldName = $role->name;
        DB::transaction(function () use ($role, $validated, $oldName): void {
            $role->update($validated);

            if ($role->name !== $oldName) {
                DB::table('users')
                    ->where('role_id', $role->role_id)
                    ->update(['role' => $role->name]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui',
            'data' => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * PUT /api/roles/{id}/permissions
     */
    public function syncPermissions(Request $request, $id)
    {
        $validated = $request->validate([
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'distinct', 'exists:permissions,permission_id'],
        ]);

        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan',
            ], 404);
        }

        $role->permissions()->sync($validated['permission_ids']);

        return response()->json([
            'success' => true,
            'message' => 'Permission role berhasil diperbarui',
            'data' => $role->load('permissions'),
        ]);
    }

    /**
     * DELETE /api/roles/{id}
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan',
            ], 404);
        }

        if ($role->name === 'Superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Role Superadmin tidak dapat dihapus.',
            ], 403);
        }

        if ($role->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak dapat dihapus karena masih digunakan oleh user lain',
            ], 409);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus',
        ]);
    }
}
