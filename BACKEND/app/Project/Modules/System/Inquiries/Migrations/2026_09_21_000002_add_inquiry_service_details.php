<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('inquiry_service_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->unique()->constrained('inquiries')->cascadeOnDelete();
            $table->string('service_type')->index();
            $table->foreignId('customer_user_id')->nullable()->constrained('users');
            $table->string('locale', 2)->default('en');
            $table->foreignId('currency_id')->nullable()->constrained('currencies');
            $table->json('contact');
            $table->json('request_details');
            $table->json('offer_snapshot')->nullable();
            $table->json('operations')->nullable();
            $table->json('agreed_price')->nullable();
            $table->foreignId('booking_id')->nullable()->unique()->constrained('bookings');
            $table->foreignId('quotation_version_id')->nullable()->constrained('quotation_versions');
            $table->timestamps();
        });
        Schema::table('quotation_versions', fn (Blueprint $table) => $table->json('service_context')->nullable());
    }

    public function down(): void
    {

        Schema::table('quotation_versions', fn (Blueprint $table) => $table->dropColumn('service_context'));
        Schema::dropIfExists('inquiry_service_details');
    }
};
