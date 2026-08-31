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
            $borrowedAt = fake()->dateTimeBetween('-2 months', 'now');
            $status = fake()->randomElement(['Borrowed', 'Returned', 'Late']);

            DB::table('book_loans')->insert([
                'book_id' => $bookIds->random(),
                'citizen_id' => $citizenIds->random(),
                'borrowed_at' => $borrowedAt->format('Y-m-d'),
                'due_date' => (clone $borrowedAt)->modify('+7 days')->format('Y-m-d'),
                'returned_at' => $status === 'Returned' ? (clone $borrowedAt)->modify('+5 days')->format('Y-m-d') : null,
                'status' => $status,
                'fine_amount' => $status === 'Late' ? fake()->randomFloat(2, 5000, 50000) : 0.00,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}