<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_days', function (Blueprint $table) {
            $table->foreignId('accommodation_id')
                ->nullable()
                ->after('destination_id')
                ->constrained('accommodations')
                ->nullOnDelete();
        });

        Schema::table('trip_days', function (Blueprint $table) {
            $table->foreignId('accommodation_id')
                ->nullable()
                ->after('destination_id')
                ->constrained('accommodations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotation_days', function (Blueprint $table) {
            $table->dropForeign(['accommodation_id']);
            $table->dropColumn('accommodation_id');
        });

        Schema::table('trip_days', function (Blueprint $table) {
            $table->dropForeign(['accommodation_id']);
            $table->dropColumn('accommodation_id');
        });
    }
};
