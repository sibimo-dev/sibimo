<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $lurahId = DB::table('staff')
            ->where('is_signer', true)
            ->where(function ($query) {
                $query->whereRaw('LOWER(position) LIKE ?', ['%lurah%'])
                    ->orWhereRaw('LOWER(position) LIKE ?', ['%kepala desa%']);
            })
            ->value('staff_id');

        if ($lurahId) {
            DB::table('letter_types')
                ->whereIn('code', ['SKTA', 'SKTH'])
                ->update(['signer_id' => $lurahId, 'updated_at' => now()]);
        }

        $jagabayaId = DB::table('staff')
            ->where('is_signer', true)
            ->whereRaw('LOWER(position) LIKE ?', ['%jagabaya%'])
            ->value('staff_id');

        if ($jagabayaId) {
            DB::table('letter_types')
                ->where('code', 'SKC')
                ->update(['signer_id' => $jagabayaId, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        // Tidak mengembalikan signer lama karena nilai sebelumnya dapat
        // berbeda antar instalasi dan sudah tidak memenuhi konfigurasi baru.
    }
};
