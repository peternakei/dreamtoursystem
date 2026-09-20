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
        Schema::create('trip_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->nullable()->constrained('trips');
            $table->foreignId('trip_group_id')->nullable()->constrained('trip_groups');
            $table->decimal('price', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('age_group_id')->nullable()->constrained('age_groups');
            $table->boolean('is_discounted')->nullable()->default(false);
            $table->foreignId('discount_type_id')->nullable()->constrained('discount_types');
            $table->decimal('discount', 15, 2)->default(0.00);
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('trip_prices');
    }
};
