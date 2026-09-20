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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('trip_code')->nullable();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->date('from_date');
            $table->date('to_date');
            $table->date('last_booking_date')->nullable();
            $table->date('last_payment_date')->nullable();
            $table->foreignId('trip_type_id')->nullable()->constrained('trip_types');
            $table->foreignId('trip_source_id')->nullable()->constrained('trip_sources');
            $table->foreignId('trip_status_id')->nullable()->constrained('trip_statuses');
            $table->boolean('is_published')->default(false);
            $table->string('publish_remarks')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
