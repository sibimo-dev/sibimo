<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookLoanSeeder extends Seeder
{
    public function run(): void
    {
        $bookIds = DB::table('books')->pluck('book_id');
        $citizenIds = DB::table('citizens')->pluck('citizen_id');

        for ($i = 0; $i < 20; $i++) {
            $borrowedAt = now()->startOfMonth()->subDays($i + 1);
            $status = ['Borrowed', 'Returned', 'Late'][$i % 3];

            DB::table('book_loans')->updateOrInsert(
                [
                    'book_id' => $bookIds[$i % $bookIds->count()],
                    'citizen_id' => $citizenIds[$i % $citizenIds->count()],
                    'borrowed_at' => $borrowedAt->format('Y-m-d'),
                ],
                [
                    'due_date' => (clone $borrowedAt)->modify('+7 days')->format('Y-m-d'),
                    'returned_at' => $status === 'Returned' ? (clone $borrowedAt)->modify('+5 days')->format('Y-m-d') : null,
                    'status' => $status,
                    'fine_amount' => $status === 'Late' ? 25000.00 : 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }
}
