<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('developments')) {
            return;
        }

        Schema::table('developments', function (Blueprint $table): void {
            if (!Schema::hasColumn('developments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('cover_image');
            }

            if (!Schema::hasColumn('developments', 'photo_0')) {
                $table->string('photo_0')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('developments', 'photo_50')) {
                $table->string('photo_50')->nullable()->after('photo_0');
            }

            if (!Schema::hasColumn('developments', 'photo_100')) {
                $table->string('photo_100')->nullable()->after('photo_50');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('developments')) {
            return;
        }

        Schema::table('developments', function (Blueprint $table): void {
            foreach (['photo_100', 'photo_50', 'photo_0', 'created_by'] as $column) {
                if (Schema::hasColumn('developments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
