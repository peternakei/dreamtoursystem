<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Vehicles\Vehicle;
use App\Project\Workspace\Services\PageService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::create(['username' => 'admin@test.local', 'password' => Hash::make('test-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $profile = SystemUser::create(['name' => 'Test administrator', 'email' => 'admin@test.local', 'phone' => '255700000001', 'created_by' => $user->id]);
        $user->update(['profile_id' => $profile->id]);
        $user->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_by' => $user->id, 'uuid' => (string) Str::uuid()]));

        return $user;
    }

    public function test_workspace_authentication_requires_valid_credentials_and_an_admin_role(): void
    {
        $this->getJson('/workspace/modules')->assertUnauthorized();
        $admin = $this->admin();
        $this->postJson('/workspace/login', ['username' => $admin->username, 'password' => 'wrong'])->assertUnprocessable()->assertJsonValidationErrors('username');
        $this->postJson('/workspace/login', ['username' => $admin->username, 'password' => 'test-password'])->assertOk()->assertJsonPath('user.username', $admin->username);
        $this->getJson('/workspace/session')->assertOk()->assertJsonStructure(['csrf_token', 'user']);
        $this->postJson('/workspace/logout')->assertOk();
        $this->assertGuest();
        $admin->removeRole('SuperAdmin');
        $this->actingAs($admin)->getJson('/workspace/modules')->assertForbidden();
    }

    public function test_vehicle_validation_and_mutations_use_the_migrated_controller(): void
    {
        $this->actingAs($this->admin());
        $this->withHeader('X-Safari-Workspace', '1')->postJson('/vehicles', [])->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->withHeader('X-Safari-Workspace', '1')->postJson('/vehicles', ['name' => 'Test safari cruiser', 'capacity' => '6 guests'])->assertOk()->assertJsonPath('status', true);
        $vehicle = Vehicle::where('name', 'Test safari cruiser')->firstOrFail();
        $this->withHeader('X-Safari-Workspace', '1')->putJson('/vehicles/'.$vehicle->uuid, ['name' => 'Updated cruiser', 'capacity' => '7 guests'])->assertOk();
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'name' => 'Updated cruiser']);
        $this->withHeader('X-Safari-Workspace', '1')->deleteJson('/vehicles/'.$vehicle->uuid)->assertOk();
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    public function test_moved_routes_and_polymorphic_names_remain_compatible(): void
    {
        $this->assertSame('/trips', parse_url(route('trips.index'), PHP_URL_PATH));
        $this->assertSame('/api/trips', parse_url(route('api.trips.index'), PHP_URL_PATH));
        $this->assertSame(User::class, Relation::getMorphedModel('App\\Models\\User'));
        $this->assertDatabaseCount('migrations', 103);
    }

    public function test_form_descriptors_exclude_csrf_values_and_external_actions(): void
    {
        $forms = PageService::forms('<form method="POST" action="/vehicles"><input name="_token" value="secret"><input name="name" required><select name="capacity"><option value="6" selected>Six</option></select><button type="submit">Save</button></form><form action="https://external.invalid/collect"><input name="password"></form>', '/vehicles');
        $this->assertCount(1, $forms);
        $this->assertSame(['name', 'capacity'], array_column($forms[0]['fields'], 'name'));
        $this->assertSame('6',$forms[0]['fields'][1]['value']);
    }
}
