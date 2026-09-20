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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->nullable();
            $table->foreignId('invoice_id')->constrained('invoices');
            $table->date('receipt_date');
            $table->decimal('amount', 15, 2)->default(0.00);
            $table->foreignId('currency_id')->constrained('currencies');
            $table->foreignId('bank_detail_id')->nullable()->constrained('bank_details');
            $table->foreignId('payment_mode_id')->constrained('payment_modes');
            $table->string('cheque_number')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('remarks');
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
        Schema::dropIfExists('receipts');
    }
};
