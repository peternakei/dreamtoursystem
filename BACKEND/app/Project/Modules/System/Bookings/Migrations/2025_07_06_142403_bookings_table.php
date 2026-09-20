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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->nullable();
            $table->foreignId('tourist_id')->constrained('tourists');
            $table->foreignId('trip_id')->constrained('trips');
            $table->foreignId('trip_group_id')->nullable()->constrained('trip_groups');
            $table->foreignId('booking_type_id')->nullable()->constrained('booking_types');
            $table->date('booking_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('vat_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->string('remarks');
            $table->string('comments')->nullable();
            $table->integer('guest_count')->default(1);
            $table->foreignId('booking_status_id')->constrained('booking_statuses');
            $table->foreignId('created_by')->nullable()->constrained('users');
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
        Schema::dropIfExists('bookings');
    }
};
