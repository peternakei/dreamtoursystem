<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Logs\AuditLogger;
use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Vehicles\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkspaceLogsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->artisan('migrate:fresh')->assertExitCode(0);
        config(['database.connections.audit_logs_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''], 'activitylog.database_connection' => 'audit_logs_test']);
        (require app_path('Project/Modules/Core/Logs/Migrations/2026_09_20_150000_create_workspace_audit_tables.php'))->up();
    }

    private function logs(string $table = 'activity_logs')
    {
        return DB::connection('audit_logs_test')->table($table);
    }

    private function admin(): User
    {
        $user = User::create(['username' => 'audit-admin@test.local', 'password' => Hash::make('test-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $profile = SystemUser::create(['name' => 'Audit administrator', 'email' => $user->username, 'phone' => '255700000002', 'created_by' => $user->id]);
        $user->update(['profile_id' => $profile->id]);
        $user->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_by' => $user->id, 'uuid' => (string) Str::uuid()]));
        return $user;
    }

    public function test_changes_are_stored_in_separate_database_only_after_commit_and_not_after_rollback(): void
    {
        $user = $this->admin();
        $this->actingAs($user);
        $vehicle = null;
        DB::transaction(function () use (&$vehicle, $user) {
            $vehicle = Vehicle::create(['name' => 'Audit cruiser', 'capacity' => '6 guests', 'created_by' => $user->id]);
            $this->assertSame(0, $this->logs()->where('module', 'Vehicle')->count());
        });
        $entry = $this->logs()->where('module', 'Vehicle')->first();
        $this->assertSame('created', $entry->action);
        $this->assertSame($user->id, $entry->user_id);
        $this->assertSame(0, DB::table('activity_logs')->count());
        $vehicle->update(['name' => 'Changed cruiser']);
        $update = $this->logs()->where('module', 'Vehicle')->where('action', 'updated')->first();
        $this->assertSame('Audit cruiser', json_decode($update->old_data, true)['name']);
        $this->assertSame('Changed cruiser', json_decode($update->new_data, true)['name']);
        DB::beginTransaction();
        $vehicle->update(['name' => 'Rolled back']);
        DB::rollBack();
        $this->assertSame(2, $this->logs()->where('module', 'Vehicle')->count());
        $vehicle->refresh()->delete();
        $this->assertSame('deleted', $this->logs()->where('module', 'Vehicle')->orderByDesc('id')->first()->action);
    }

    public function test_authentication_and_request_correlation_never_store_credentials(): void
    {
        $user = $this->admin();
        $this->postJson('/workspace/login', ['username' => $user->username, 'password' => 'wrong-secret'])->assertUnprocessable();
        $this->assertSame(1, $this->logs()->where('action', 'login_failed')->count());
        $response = $this->postJson('/workspace/login', ['username' => $user->username, 'password' => 'test-password'])->assertOk();
        $requestId = $response->headers->get('X-Request-ID');
        $this->assertNotNull($requestId);
        $this->assertSame(1, $this->logs()->where('request_id', $requestId)->where('action', 'login')->count());
        $request = $this->logs('request_logs')->where('request_id', $requestId)->first();
        $this->assertSame(200, $request->response_status);
        $this->assertSame($user->id, $request->user_id);
        $data = json_encode([$this->logs()->get(), $this->logs('request_logs')->get()]);
        foreach (['wrong-secret', 'test-password', $user->getRawOriginal('password')] as $secret) $this->assertStringNotContainsString($secret, $data);
        $this->postJson('/workspace/logout')->assertOk();
        $this->assertSame($user->id, $this->logs()->where('action', 'logout')->first()->user_id);
    }

    public function test_log_ui_api_requires_admin_and_filters_the_full_history(): void
    {
        $this->getJson('/workspace/logs/activity')->assertUnauthorized();
        $admin = $this->admin();
        $this->actingAs($admin);
        for ($i = 0; $i < 31; $i++) {
            AuditLogger::write('activity_logs', array_merge(AuditLogger::context(), [
                'occurred_at' => '2026-09-19 23:59:59.999999', 'module' => 'FilterFixture', 'action' => 'created', 'message' => 'Fixture '.$i,
            ]));
        }
        $response = $this->getJson('/workspace/logs/activity?start_date=2026-09-19&end_date=2026-09-19&search=FilterFixture&per_page=25')
            ->assertOk()->assertJsonPath('total', 31)->assertJsonPath('last_page', 2)->assertJsonCount(25, 'data')->assertJsonPath('data.0.user_name', $admin->username);
        $this->getJson('/workspace/logs/activity?search=FilterFixture&page=2')->assertJsonCount(6, 'data');
        $this->getJson('/workspace/logs/activity?search=FilterFixture&start_date=2026-09-20')->assertJsonPath('total', 0);
        $uuid = $response->json('data.0.uuid');
        $this->getJson('/workspace/logs/activity/'.$uuid)->assertOk()->assertJsonPath('data.module', 'FilterFixture');
        $this->getJson('/workspace/logs/activity?start_date=2026-09-20&end_date=2026-09-19')->assertUnprocessable();
        $this->getJson('/workspace/logs/unknown')->assertNotFound();
        $admin->removeRole('SuperAdmin');
        $this->getJson('/workspace/logs/activity')->assertForbidden();
        $this->getJson('/workspace/logs/activity/'.$uuid)->assertForbidden();
    }

    public function test_errors_are_correlated_without_logging_raw_exception_secrets(): void
    {
        Route::middleware('web')->get('/audit-test-exception', fn () => throw new \RuntimeException('password=must-not-be-logged'));
        $response = $this->getJson('/audit-test-exception')->assertStatus(500);
        $requestId = $response->headers->get('X-Request-ID');
        $entry = $this->logs('error_logs')->where('request_id', $requestId)->first();
        $this->assertNotNull($entry);
        $this->assertSame(\RuntimeException::class, $entry->message);
        $this->assertStringNotContainsString('must-not-be-logged', json_encode($entry));
        $this->assertSame(500, $this->logs('request_logs')->where('request_id', $requestId)->first()->response_status);
    }

    public function test_redaction_handles_nested_values_and_storage_failure_does_not_break_requests(): void
    {
        $result = AuditLogger::redact(['settings' => '{"password":"hidden"}', 'authorization' => ['nested' => 'hidden'], 'value' => 'hidden', 'name' => 'Visible']);
        $this->assertStringNotContainsString('hidden', json_encode($result));
        $this->assertSame('Visible', $result['name']);
        config(['activitylog.database_connection' => 'nonexistent']);
        $this->getJson('/workspace/session')->assertOk();
        config(['activitylog.database_connection' => 'audit_logs_test']);
    }
}
