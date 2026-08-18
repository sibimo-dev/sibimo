<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class FeedbackController extends Controller
{
    public function index(): JsonResponse
    {
        $feedbacks = Feedback::query()->latest('submitted_at')->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data feedback berhasil diambil.',
            'data' => $feedbacks,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required','string','max:100'],
            'email' => ['required','email','max:100'],
            'message' => ['required','string']
        ]);

        $feedback = Feedback::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Feedback berhasil dikirim.',
            'data' => $feedback,
        ], 201);
    }

    public function show(int $feedback_id): JsonResponse
    {
        $feedback = Feedback::query()->findOrFail($feedback_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail feedback berhasil diambil.',
            'data' => $feedback,
        ]);
    }

    public function update(Request $request, int $feedback_id): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedback_id);

        $validated = $request->validate([
            'status' => ['sometimes','required', Rule::in(['Unread','Read','Replied'])]
        ]);

        $feedback->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Status feedback berhasil diperbarui.',
            'data' => $feedback,
        ]);
    }

    public function destroy(int $feedback_id): JsonResponse
    {
        $feedback = Feedback::findOrFail($feedback_id);
        $feedback->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Feedback berhasil dihapus.',
        ]);
    }

}
