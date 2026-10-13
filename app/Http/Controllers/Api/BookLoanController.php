<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookLoan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;


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
            'due_date' => ['required','date','after_or_equal:borrowed_at'],
        ]);

        $loan = DB::transaction(function () use ($validated) {
            $book = Book::query()->lockForUpdate()->findOrFail($validated['book_id']);

            if ($book->stock < 1) {
                throw ValidationException::withMessages([
                    'book_id' => ['Stok buku tidak tersedia.'],
                ]);
            }

            $loan = BookLoan::create([
                ...$validated,
                'status' => 'Borrowed',
                'fine_amount' => 0,
            ]);

            $book->decrement('stock');

            return $loan;
        });

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman buku berhasil dicatat.',
            'data' => $loan->load(['book', 'citizen']),
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
            'returned_at' => ['required','date'],
            'status' => ['required', Rule::in(['Returned','Late'])],
            'fine_amount' => ['required','numeric','min:0'],
        ]);

        if ($validated['returned_at'] < $loan->borrowed_at->toDateString()) {
            throw ValidationException::withMessages([
                'returned_at' => ['Tanggal pengembalian tidak boleh sebelum tanggal pinjam.'],
            ]);
        }

        $loan = DB::transaction(function () use ($loan_id, $validated) {
            $loan = BookLoan::query()->lockForUpdate()->findOrFail($loan_id);

            if ($loan->status !== 'Borrowed') {
                throw ValidationException::withMessages([
                    'status' => ['Peminjaman ini sudah dikembalikan.'],
                ]);
            }

            $book = Book::query()->lockForUpdate()->findOrFail($loan->book_id);

            $loan->update($validated);
            $book->increment('stock');

            return $loan;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil diperbarui.',
            'data' => $loan->load(['book', 'citizen']),
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
