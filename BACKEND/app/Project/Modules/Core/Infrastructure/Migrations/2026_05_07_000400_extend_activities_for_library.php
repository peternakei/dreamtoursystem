<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'color')) {
                $table->string('color', 20)->nullable()->after('description');
            }
            if (!Schema::hasColumn('activities', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            if (Schema::hasColumn('activities', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
            if (Schema::hasColumn('activities', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
