<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotation_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->foreignId('trip_id')->nullable()->constrained('trips')->nullOnDelete();
            $table->unsignedInteger('version_number')->default(1);
            $table->string('reference_number')->unique();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->longText('introduction')->nullable();
            $table->longText('company_profile')->nullable();
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->foreignId('season_id')->nullable()->constrained('seasons')->nullOnDelete();
            $table->foreignId('service_class_id')->nullable()->constrained('service_classes')->nullOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedInteger('duration_days')->default(0);
            $table->unsignedInteger('duration_nights')->default(0);
            $table->unsignedInteger('guest_count')->default(1);
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->boolean('hide_price_breakdown')->default(false);
            $table->boolean('hide_total_price')->default(false);
            $table->boolean('hide_terms')->default(false);
            $table->boolean('hide_payment_terms')->default(false);
            $table->string('status')->default('draft');
            $table->string('public_token')->nullable()->unique();
            $table->boolean('public_url_enabled')->default(false);
            $table->timestamp('public_expires_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->text('internal_notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_version_id')->constrained('quotation_versions')->cascadeOnDelete();
            $table->foreignId('trip_day_id')->nullable()->constrained('trip_days')->nullOnDelete();
            $table->unsignedInteger('day_number')->default(1);
            $table->date('travel_date')->nullable();
            $table->string('title')->nullable();
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->nullOnDelete();
            $table->string('accommodation_name')->nullable();
            $table->text('accommodation_notes')->nullable();
            $table->string('stay_type')->nullable();
            $table->unsignedInteger('nights')->default(0);
            $table->boolean('breakfast')->default(false);
            $table->boolean('lunch')->default(false);
            $table->boolean('dinner')->default(false);
            $table->longText('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_day_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_day_id')->constrained('quotation_days')->cascadeOnDelete();
            $table->foreignId('trip_day_activity_id')->nullable()->constrained('trip_day_activities')->nullOnDelete();
            $table->foreignId('activity_id')->nullable()->constrained('activities')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(false);
            $table->decimal('price', 15, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_price_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_version_id')->constrained('quotation_versions')->cascadeOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description');
            $table->string('traveler_type')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->nullOnDelete();
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_version_id')->constrained('quotation_versions')->cascadeOnDelete();
            $table->string('type')->default('included');
            $table->string('title')->nullable();
            $table->text('description');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_payment_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_version_id')->constrained('quotation_versions')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('quotation_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_version_id')->constrained('quotation_versions')->cascadeOnDelete();
            $table->string('sent_to')->nullable();
            $table->string('sent_from')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('channel')->default('manual');
            $table->string('status')->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'current_version_id')) {
                $table->foreignId('current_version_id')->nullable()->after('created_from_trip_id')->constrained('quotation_versions')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'current_version_id')) {
                $table->dropForeign(['current_version_id']);
                $table->dropColumn('current_version_id');
            }
        });

        Schema::dropIfExists('quotation_shares');
        Schema::dropIfExists('quotation_payment_terms');
        Schema::dropIfExists('quotation_terms');
        Schema::dropIfExists('quotation_price_lines');
        Schema::dropIfExists('quotation_day_activities');
        Schema::dropIfExists('quotation_days');
        Schema::dropIfExists('quotation_versions');
    }
};
