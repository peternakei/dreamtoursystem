<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotation_versions', function (Blueprint $table) {
            // Per-quote editable highlights (newline-separated bullet list).
            $table->text('highlights')->nullable()->after('introduction');

            // Agent's personal intro letter rendered on the cover page.
            // Falls back to `introduction` when null, then to a default.
            $table->text('agent_intro_letter')->nullable()->after('highlights');

            // Linked booking once the quote is accepted and converted via the
            // public booking flow (POST /api/save_booking).
            $table->unsignedBigInteger('booking_id')->nullable()->after('accepted_at');
            $table->index('booking_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotation_versions', function (Blueprint $table) {
            $table->dropIndex(['booking_id']);
            $table->dropColumn(['highlights', 'agent_intro_letter', 'booking_id']);
        });
    }
};
