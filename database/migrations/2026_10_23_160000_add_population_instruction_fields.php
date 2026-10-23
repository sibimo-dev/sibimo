<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizens', function (Blueprint $table): void {
            $table->string('title_code', 10)->nullable()->after('record_event');
            $table->text('previous_address')->nullable()->after('ktp_address');
            $table->string('passport_number', 50)->nullable()->after('previous_address');
            $table->date('passport_expiry_date')->nullable()->after('passport_number');
            $table->string('belief_organization', 150)->nullable()->after('religion');
            $table->string('rt_head_name', 100)->nullable()->after('rw');
            $table->string('rw_head_name', 100)->nullable()->after('rt_head_name');
            $table->unsignedSmallInteger('record_year')->nullable()->after('record_event');
        });

        Schema::create('citizen_event_counters', function (Blueprint $table): void {
            $table->id();
            $table->string('event_type', 20);
            $table->unsignedSmallInteger('event_year');
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
            $table->unique(['event_type', 'event_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citizen_event_counters');

        Schema::table('citizens', function (Blueprint $table): void {
            $table->dropColumn([
                'title_code',
                'record_year',
                'previous_address',
                'passport_number',
                'passport_expiry_date',
                'belief_organization',
                'rt_head_name',
                'rw_head_name',
            ]);
        });
    }
};
