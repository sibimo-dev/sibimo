<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * GET /api/permissions
     */
    public function index()
    {
        $permissions = Permission::orderBy('name')->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Daftar permission berhasil diambil',
            'data' => $permissions,
        ]);
    }

    /**
     * GET /api/permissions/{id}
     */
    public function show($id)
    {
        $permission = Permission::with('roles')->find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail permission berhasil diambil',
            'data' => $permission,
        ]);
    }

    /**
     * POST /api/permissions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dibuat',
            'data' => $permission,
        ], 201);
    }

    /**
     * PUT/PATCH /api/permissions/{id}
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->ignore($permission->permission_id, 'permission_id'),
            ],
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diperbarui',
            'data' => $permission,
        ]);
    }

    /**
     * DELETE /api/permissions/{id}
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission tidak ditemukan',
            ], 404);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dihapus',
        ]);
    }
}
