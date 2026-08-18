<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class BookController extends Controller
{
    public function index(): JsonResponse
    {
        $books = Book::query()->with(['category'])->latest()->get();

        return response()->json([
            'succes' => true,
            'message' => 'Data buku berhasil diambil.',
            'data' => $books,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required','exists:book_categories,category_id'],
            'title' => ['required','string','max:200'],
            'author' => ['nullable','string','max:150'],
            'isbn' => ['nullable','string','max:20'],
            'stock' => ['integer','min:0']
        ]);

        $book = Book::create($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Buku berhasil ditambahkan.',
            'data' => $book,
        ], 201);
    }

    public function show(int $book_id): JsonResponse
    {
        $book = Book::query()->with(['category'])->findOrFail($book_id);

        return response()->json([
            'succes' => true,
            'message' => 'Detail buku berhasil diambil.',
            'data' => $book,
        ]);
    }

    public function update(Request $request, int $book_id): JsonResponse
    {
        $book = Book::findOrFail($book_id);

        $validated = $request->validate([
            'category_id' => ['sometimes','required','exists:book_categories,category_id'],
            'title' => ['sometimes','required','string','max:200'],
            'author' => ['nullable','string','max:150'],
            'isbn' => ['nullable','string','max:20'],
            'stock' => ['integer','min:0']
        ]);

        $book->update($validated);

        return response()->json([
            'succes' => true,
            'message' => 'Buku berhasil diperbarui.',
            'data' => $book,
        ]);
    }

    public function destroy(int $book_id): JsonResponse
    {
        $book = Book::findOrFail($book_id);
        $book->delete();

        return response()->json([
            'succes' => true,
            'message' => 'Buku berhasil dihapus.',
        ]);
    }

}
