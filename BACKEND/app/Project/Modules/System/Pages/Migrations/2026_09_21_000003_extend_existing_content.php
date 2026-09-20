<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::table('pages', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('sections')->nullable();
            $table->json('translations')->nullable();
            $table->json('seo')->nullable();
        });
        Schema::table('trips', function (Blueprint $table) {
            $table->json('service_details')->nullable();
            $table->json('translations')->nullable();
        });
        Schema::table('destinations', fn (Blueprint $table) => $table->json('translations')->nullable());
    }

    public function down(): void
    {

        Schema::table('destinations', fn (Blueprint $table) => $table->dropColumn('translations'));
        Schema::table('trips', fn (Blueprint $table) => $table->dropColumn(['service_details', 'translations']));
        Schema::table('pages', fn (Blueprint $table) => $table->dropColumn(['is_published', 'sort_order', 'sections', 'translations', 'seo']));
    }
};
