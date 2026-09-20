<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $schema = Schema::connection(config('activitylog.database_connection'));
        foreach (['activity_logs', 'request_logs', 'error_logs'] as $name) {
            $schema->create($name, function (Blueprint $table) use ($name) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->uuid('request_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->timestamp('occurred_at', 6)->index();
                $table->timestamps();
                if ($name === 'activity_logs') {
                    $table->string('module')->index();
                    $table->string('record_id')->nullable()->index();
                    $table->string('action')->index();
                    $table->string('type')->default('SYS_LOG');
                    $table->text('message');
                    $table->json('old_data')->nullable();
                    $table->json('new_data')->nullable();
                } elseif ($name === 'request_logs') {
                    $table->string('method', 10);
                    $table->text('url');
                    $table->string('route_name')->nullable();
                    $table->string('ip_address', 45)->nullable();
                    $table->unsignedSmallInteger('response_status')->index();
                    $table->unsignedInteger('duration');
                    $table->json('payload')->nullable();
                } else {
                    $table->string('module')->nullable();
                    $table->string('action')->nullable();
                    $table->text('message');
                    $table->text('stack_trace');
                    $table->json('context')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['error_logs', 'request_logs', 'activity_logs'] as $name) {
            Schema::connection(config('activitylog.database_connection'))->dropIfExists($name);
        }
    }
};
