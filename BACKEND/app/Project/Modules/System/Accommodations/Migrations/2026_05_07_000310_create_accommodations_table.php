<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 200);
            $table->foreignId('stay_type_id')->nullable()->constrained('stay_types')->nullOnDelete();
            $table->foreignId('primary_destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('location_text', 200)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('accommodation_destination', function (Blueprint $table) {
            $table->foreignId('accommodation_id')->constrained('accommodations')->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained('destinations')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->primary(['accommodation_id', 'destination_id']);
            $table->index(['destination_id', 'accommodation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_destination');
        Schema::dropIfExists('accommodations');
    }
};
