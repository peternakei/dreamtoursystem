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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tourist_id')->constrained('tourists');
            $table->string('cartable_type'); // trip or destination
            $table->unsignedBigInteger('cartable_id');
            $table->string('cartable_uuid'); // UUID of the trip or destination
            $table->integer('quantity')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['cartable_type', 'cartable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
