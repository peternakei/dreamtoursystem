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
        Schema::table('ratings', function (Blueprint $table) {
              // Drop the existing foreign key constraint
            $table->dropForeign(['trip_category_activity_id']);
            
            // Rename the column
            $table->renameColumn('trip_category_activity_id', 'destination_id');
            
            // Add the new foreign key constraint
            $table->foreign('destination_id')
                  ->references('id')
                  ->on('destinations')
                  ->nullable()
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['destination_id']);
            
            // Rename the column back
            $table->renameColumn('destination_id', 'trip_category_activity_id');
            
            // Restore the original foreign key constraint
            $table->foreign('trip_category_activity_id')
                  ->references('id')
                  ->on('trip_category_activities')
                  ->nullable()
                  ->onDelete('cascade');
        });
    }
};
