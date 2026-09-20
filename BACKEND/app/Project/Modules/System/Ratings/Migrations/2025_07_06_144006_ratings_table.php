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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->nullable();
            $table->foreignId('trip_group_id')->nullable();
            $table->foreignId('trip_category_activity_id')->nullable()->constrained('trip_category_activities');
            $table->foreignId('tourist_id')->constrained('tourists');
            $table->float('rating');
            $table->text('review');
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
        Schema::dropIfExists('ratings');
    }
};
