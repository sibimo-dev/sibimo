<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Data staff berhasil diambil.',
            'data' => Staff::query()
                ->orderBy('name')
                ->get(['staff_id', 'name', 'position']),
        ]);
    }
}