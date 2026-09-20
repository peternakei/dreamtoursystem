<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('vehicles', function (Blueprint $table) {
            $table->json('rental_specs')->nullable();
            $table->boolean('is_rental_published')->default(false)->index();
            $table->json('translations')->nullable();
        });
        Schema::create('rental_offers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('purpose');
            $table->string('vehicle_type');
            $table->json('vehicle_uuids')->nullable();
            $table->string('price_unit')->default('day');
            $table->string('driver_policy')->default('included');
            $table->string('fuel_policy')->default('excluded');
            $table->json('details')->nullable();
            $table->json('translations')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {

        Schema::dropIfExists('rental_offers');
        Schema::table('vehicles', fn (Blueprint $table) => $table->dropColumn(['rental_specs', 'is_rental_published', 'translations']));
    }
};
