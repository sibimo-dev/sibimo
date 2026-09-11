<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;

class PermissionController extends Controller
{
    /** GET /api/permissions */
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();

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

}
