<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            // Drop existing foreign key columns
            $table->dropForeign(['trip_id']);
            $table->dropForeign(['trip_group_id']);
            $table->dropForeign(['destination_id']);

            // Drop existing columns
            $table->dropColumn(['trip_id', 'trip_group_id', 'destination_id']);

            // Add new polymorphic columns
            $table->string('likeable_type')->after('tourist_id');
            $table->unsignedBigInteger('likeable_id')->after('likeable_type');
            $table->string('likeable_uuid')->after('likeable_id');

            // Add index for polymorphic relationship
            $table->index(['likeable_type', 'likeable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            // Remove new columns
            $table->dropIndex(['likeable_type', 'likeable_id']);
            $table->dropColumn(['likeable_type', 'likeable_id', 'likeable_uuid']);

            // Restore original columns
            $table->foreignId('trip_id')->nullable()->constrained('trips');
            $table->foreignId('trip_group_id')->nullable()->constrained('trip_groups');
            $table->foreignId('destination_id')->nullable()->constrained('destinations');
        });
    }
};
