<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $letterTypeId = DB::table('letter_types')
            ->where('code', 'PNP')
            ->value('letter_type_id');

        if (! $letterTypeId) {
            return;
        }

        DB::table('letter_type_fields')->updateOrInsert(
            [
                'letter_type_id' => $letterTypeId,
                'field_key' => 'status_catin_putra',
            ],
            [
                'section_name' => 'Data Catin Putra',
                'field_label' => 'Status',
                'field_type' => 'select',
                'is_required' => true,
                'options' => json_encode(['Jejaka', 'Duda', 'Beristri']),
                'sort_order' => 60,
                'created_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        $letterTypeId = DB::table('letter_types')
            ->where('code', 'PNP')
            ->value('letter_type_id');

        if ($letterTypeId) {
            DB::table('letter_type_fields')
                ->where('letter_type_id', $letterTypeId)
                ->where('field_key', 'status_catin_putra')
                ->delete();
        }
    }
};
