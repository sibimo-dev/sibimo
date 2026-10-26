<?php

use App\Support\LetterClassificationIndexCatalog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('letter_types', 'classification_index')) {
            Schema::table('letter_types', function (Blueprint $table): void {
                $table->string('classification_index', 20)->nullable()->after('number_prefix');
                $table->index('classification_index');
            });
        }

        foreach (LetterClassificationIndexCatalog::map() as $code => $index) {
            DB::table('letter_types')
                ->where('code', $code)
                ->update(['classification_index' => $index]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('letter_types', 'classification_index')) {
            Schema::table('letter_types', function (Blueprint $table): void {
                $table->dropIndex(['classification_index']);
                $table->dropColumn('classification_index');
            });
        }
    }
};
