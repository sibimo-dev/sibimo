<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * GET /api/user-permissions
     * Optional query: ?user_id=1
     */
    public function index(Request $request)
    {
        $query = UserPermission::with(['user', 'permissions']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        $userPermissions = $query->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Daftar permission user berhasil diambil',
            'data' => $userPermissions,
        ]);
    }

    /**
     * POST /api/user-permissions
     *
     * CATATAN PENTING: Primary key model UserPermission diset ke
     * 'user_id' (bukan id auto-increment tersendiri, dan bukan
     * composite key user_id+permission_id). Artinya secara struktur,
     * satu user hanya bisa punya SATU baris di tabel ini — kalau
     * tujuannya satu user boleh punya banyak permission tambahan
     * (many-to-many yang sebenarnya), ini kemungkinan bug di model/
     * migration. Mohon dicek ke migration aslinya / dikonfirmasi ke
     * senior/rekanmu. Untuk sementara, endpoint ini saya buat agar
     * mencegah duplikasi kombinasi user_id + permission_id di level
     * aplikasi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,user_id',
            'permission_id' => 'required|integer|exists:permissions,permission_id',
        ]);

        $exists = UserPermission::where('user_id', $validated['user_id'])
            ->where('permission_id', $validated['permission_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah memiliki permission ini',
            ], 409);
        }

        $userPermission = UserPermission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diberikan ke user',
            'data' => $userPermission,
        ], 201);
    }

    /**
     * DELETE /api/user-permissions
     * Body: { "user_id": 1, "permission_id": 2 }
     *
     * Menggunakan kombinasi user_id + permission_id di body request
     * karena primary key tabel ini tidak unik per baris permission.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'permission_id' => 'required|integer',
        ]);

        $userPermission = UserPermission::where('user_id', $validated['user_id'])
            ->where('permission_id', $validated['permission_id'])
            ->first();

        if (!$userPermission) {
            return response()->json([
                'success' => false,
                'message' => 'Data permission user tidak ditemukan',
            ], 404);
        }

        UserPermission::where('user_id', $validated['user_id'])
            ->where('permission_id', $validated['permission_id'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dicabut dari user',
        ]);
    }
}
