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
            // Add new fields for inquiry details only if they don't exist
            if (!Schema::hasColumn('inquiries', 'trip_type_id')) {
                $table->foreignId('trip_type_id')->nullable()->constrained('trip_types');
            }
            if (!Schema::hasColumn('inquiries', 'destinations')) {
                $table->json('destinations')->nullable(); // Array of destination IDs
            }
            if (!Schema::hasColumn('inquiries', 'locations')) {
                $table->json('locations')->nullable(); // Array of location IDs
            }
            if (!Schema::hasColumn('inquiries', 'service_class_id')) {
                $table->foreignId('service_class_id')->nullable()->constrained('service_classes');
            }
            if (!Schema::hasColumn('inquiries', 'guests')) {
                $table->integer('guests')->default(1);
            }
            if (!Schema::hasColumn('inquiries', 'budget')) {
                $table->string('budget')->nullable();
            }
            if (!Schema::hasColumn('inquiries', 'status')) {
                $table->string('status')->default('pending');
            }
            if (!Schema::hasColumn('inquiries', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users'); // For logged-in users
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            // Revert changes - only drop columns that exist
            if (Schema::hasColumn('inquiries', 'trip_type_id')) {
                $table->dropForeign(['trip_type_id']);
                $table->dropColumn('trip_type_id');
            }
            if (Schema::hasColumn('inquiries', 'destinations')) {
                $table->dropColumn('destinations');
            }
            if (Schema::hasColumn('inquiries', 'locations')) {
                $table->dropColumn('locations');
            }
            if (Schema::hasColumn('inquiries', 'service_class_id')) {
                $table->dropForeign(['service_class_id']);
                $table->dropColumn('service_class_id');
            }
            if (Schema::hasColumn('inquiries', 'guests')) {
                $table->dropColumn('guests');
            }
            if (Schema::hasColumn('inquiries', 'budget')) {
                $table->dropColumn('budget');
            }
            if (Schema::hasColumn('inquiries', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('inquiries', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
