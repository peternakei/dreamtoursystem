<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Logs\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogConnectionTest extends TestCase
{
    public function test_logs_use_a_separate_database_and_resolve_primary_records_lazily_and_eagerly(): void
    {
        config(['database.connections.audit_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ], 'activitylog.database_connection' => 'audit_test']);

        Schema::create('audit_test_people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::connection('audit_test')->create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');
            $table->json('properties')->nullable();
            $table->string('event')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
        });

        $person = AuditTestPerson::create(['name' => 'Audit test person']);
        $entry = activity()->performedOn($person)->causedBy($person)->log('Separate database check');

        $this->assertInstanceOf(Activity::class, $entry);
        $this->assertSame(1, DB::connection('audit_test')->table('activity_log')->count());
        $this->assertFalse(Schema::connection('audit_test')->hasTable('audit_test_people'));
        $lazy = Activity::findOrFail($entry->id);
        $this->assertSame($person->name, $lazy->subject->name);
        $this->assertSame($person->name, $lazy->causer->name);
        $eager = Activity::with(['subject', 'causer'])->findOrFail($entry->id);
        $this->assertSame($person->id, $eager->subject->id);
        $this->assertSame($person->id, $eager->causer->id);
    }

    public function test_unset_activity_connection_uses_the_main_database(): void
    {
        config(['activitylog.database_connection' => null]);
        $this->assertSame(config('database.default'), (new Activity)->getConnection()->getName());
    }
}

class AuditTestPerson extends Model
{
    protected $table = 'audit_test_people';

    protected $guarded = [];

    public $timestamps = false;
}
