<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BookLoan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class BookLoanController extends Controller
{
    public function index(): JsonResponse
    {
        $loans = BookLoan::query()->with(['book', 'citizen'])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman buku berhasil diambil.',
            'data' => $loans,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'book_id' => ['required','exists:books,book_id'],
            'citizen_id' => ['required','exists:citizens,citizen_id'],
            'borrowed_at' => ['required','date'],
            'due_date' => ['required','date'],
            'status' => ['nullable', Rule::in(['Borrowed','Returned','Late'])],
            'fine_amount' => ['nullable','numeric','min:0']
        ]);

        $loan = BookLoan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman buku berhasil dicatat.',
            'data' => $loan,
        ], 201);
    }


    public function show(int $loan_id): JsonResponse
    {
        $loan = BookLoan::query()->with(['book', 'citizen'])->findOrFail($loan_id);

        return response()->json([
            'success' => true,
            'message' => 'Detail peminjaman buku berhasil diambil.',
            'data' => $loan,
        ]);
    }

    public function update(Request $request, int $loan_id): JsonResponse
    {
        $loan = BookLoan::findOrFail($loan_id);

        $validated = $request->validate([
            'returned_at' => ['nullable','date'],
            'status' => ['sometimes','required', Rule::in(['Borrowed','Returned','Late'])],
            'fine_amount' => ['nullable','numeric','min:0']
        ]);

        $loan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diperbarui.',
            'data' => $loan,
        ]);
    }

    public function destroy(int $loan_id): JsonResponse
    {
        $loan = BookLoan::findOrFail($loan_id);
        $loan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil dihapus.',
        ]);
    }

}
