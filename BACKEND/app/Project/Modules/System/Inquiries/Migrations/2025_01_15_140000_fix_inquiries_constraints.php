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
        Schema::table('inquiries', function (Blueprint $table) {
            // Drop existing foreign key constraints if they exist
            if (Schema::hasColumn('inquiries', 'created_by')) {
                try {
                    $table->dropForeign(['created_by']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            }

            if (Schema::hasColumn('inquiries', 'updated_by')) {
                try {
                    $table->dropForeign(['updated_by']);
                } catch (\Exception $e) {
                    // Constraint might not exist
                }
            }
        });

        Schema::table('inquiries', function (Blueprint $table) {
            // Recreate the columns with proper nullable constraints
            if (Schema::hasColumn('inquiries', 'created_by')) {
                $table->foreignId('created_by')->nullable()->change();
            }

            if (Schema::hasColumn('inquiries', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->change();
            }
        });

        Schema::table('inquiries', function (Blueprint $table) {
            // Re-add foreign key constraints
            if (Schema::hasColumn('inquiries', 'created_by')) {
                $table->foreign('created_by')->references('id')->on('users');
            }

            if (Schema::hasColumn('inquiries', 'updated_by')) {
                $table->foreign('updated_by')->references('id')->on('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this as we're just fixing constraints
    }
};
