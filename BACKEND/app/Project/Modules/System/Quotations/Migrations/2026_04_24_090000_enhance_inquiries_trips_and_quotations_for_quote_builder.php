<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('inquiries', 'request_reference')) {
                $table->string('request_reference')->nullable()->after('inquiry_code');
            }

            if (!Schema::hasColumn('inquiries', 'source')) {
                $table->string('source')->nullable()->after('status');
            }

            if (!Schema::hasColumn('inquiries', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('inquiries', 'communication_language')) {
                $table->string('communication_language')->nullable()->after('source');
            }

            if (!Schema::hasColumn('inquiries', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('communication_language');
            }

            if (!Schema::hasColumn('inquiries', 'tour_title')) {
                $table->string('tour_title')->nullable()->after('description');
            }

            if (!Schema::hasColumn('inquiries', 'client_message')) {
                $table->longText('client_message')->nullable()->after('comments');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('to_date');
            }

            if (!Schema::hasColumn('trips', 'duration_nights')) {
                $table->unsignedInteger('duration_nights')->nullable()->after('duration_days');
            }
        });

        Schema::table('quotations', function (Blueprint $table) {
            if (!Schema::hasColumn('quotations', 'created_from_trip_id')) {
                $table->foreignId('created_from_trip_id')->nullable()->after('inquiry_id')->constrained('trips')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            if (Schema::hasColumn('quotations', 'created_from_trip_id')) {
                $table->dropForeign(['created_from_trip_id']);
                $table->dropColumn('created_from_trip_id');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'duration_nights')) {
                $table->dropColumn('duration_nights');
            }

            if (Schema::hasColumn('trips', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
        });

        Schema::table('inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('inquiries', 'assigned_to')) {
                $table->dropForeign(['assigned_to']);
                $table->dropColumn('assigned_to');
            }

            foreach ([
                'request_reference',
                'source',
                'communication_language',
                'received_at',
                'tour_title',
                'client_message',
            ] as $column) {
                if (Schema::hasColumn('inquiries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
