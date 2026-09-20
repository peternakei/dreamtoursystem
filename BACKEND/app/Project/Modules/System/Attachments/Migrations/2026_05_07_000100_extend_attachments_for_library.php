<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('role', 20)->default('gallery')->after('attachment_type_id');
            $table->string('title', 150)->nullable()->after('role');
            $table->unsignedInteger('sort_order')->default(0)->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['role', 'title', 'sort_order']);
        });
    }
};
