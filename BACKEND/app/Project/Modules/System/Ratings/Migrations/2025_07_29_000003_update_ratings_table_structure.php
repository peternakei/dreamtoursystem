<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check what columns actually exist in the ratings table
        $columns = Schema::getColumnListing('ratings');

        // Drop foreign keys only if they exist (using raw SQL)
        if (in_array('trip_id', $columns)) {
            try {
                DB::statement('ALTER TABLE ratings DROP FOREIGN KEY IF EXISTS ratings_trip_id_foreign');
            } catch (Exception $e) {
                // Continue if foreign key doesn't exist
            }
        }

        if (in_array('trip_group_id', $columns)) {
            try {
                DB::statement('ALTER TABLE ratings DROP FOREIGN KEY IF EXISTS ratings_trip_group_id_foreign');
            } catch (Exception $e) {
                // Continue if foreign key doesn't exist
            }
        }

        if (in_array('trip_category_activity_id', $columns)) {
            try {
                DB::statement('ALTER TABLE ratings DROP FOREIGN KEY IF EXISTS ratings_trip_category_activity_id_foreign');
            } catch (Exception $e) {
                // Continue if foreign key doesn't exist
            }
        }

        // Drop columns only if they exist
        $columnsToDrop = [];
        if (in_array('trip_id', $columns)) $columnsToDrop[] = 'trip_id';
        if (in_array('trip_group_id', $columns)) $columnsToDrop[] = 'trip_group_id';
        if (in_array('trip_category_activity_id', $columns)) $columnsToDrop[] = 'trip_category_activity_id';

        if (!empty($columnsToDrop)) {
            Schema::table('ratings', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }

        // Add new columns only if they don't exist
        Schema::table('ratings', function (Blueprint $table) use ($columns) {
            if (!in_array('rateable_type', $columns)) {
                $table->string('rateable_type')->after('tourist_id');
            }

            if (!in_array('rateable_id', $columns)) {
                $table->unsignedBigInteger('rateable_id')->after('rateable_type');
            }

            if (!in_array('rateable_uuid', $columns)) {
                $table->string('rateable_uuid')->after('rateable_id');
            }

            // Add index for polymorphic relationship
            $table->index(['rateable_type', 'rateable_id']);
        });

        // Rename review column to comment only if it exists and comment doesn't
        if (in_array('review', $columns) && !in_array('comment', $columns)) {
            Schema::table('ratings', function (Blueprint $table) {
                $table->renameColumn('review', 'comment');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Remove new columns
            $table->dropIndex(['rateable_type', 'rateable_id']);
            $table->dropColumn(['rateable_type', 'rateable_id', 'rateable_uuid']);

            // Restore original columns
            $table->foreignId('trip_id')->nullable()->constrained('trips');
            $table->foreignId('trip_group_id')->nullable()->constrained('trip_groups');
            $table->foreignId('trip_category_activity_id')->nullable()->constrained('trip_category_activities');

            // Rename comment column back to review
            $table->renameColumn('comment', 'review');
        });
    }
};
