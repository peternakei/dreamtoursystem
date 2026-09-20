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
        $this->assertDatabaseHas('migrations', ['migration' => '2026_09_20_150000_create_workspace_audit_tables']);
    }

    public function test_form_descriptors_exclude_csrf_values_and_external_actions(): void
    {
        $forms = PageService::forms('<form method="POST" action="/vehicles"><input name="_token" value="secret"><input name="name" required><select name="capacity"><option value="6" selected>Six</option></select><button type="submit">Save</button></form><form action="https://external.invalid/collect"><input name="password"></form>', '/vehicles');
        $this->assertCount(1, $forms);
        $this->assertSame(['name', 'capacity'], array_column($forms[0]['fields'], 'name'));
        $this->assertSame('6',$forms[0]['fields'][1]['value']);
    }

    public function test_inquiry_workspace_supplies_customer_assignment_and_quotation_details(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin);
        $tourist = \App\Project\Modules\System\Tourists\Tourist::create(['country_id' => \App\Project\Modules\Core\Countries\Country::create(['name' => 'Tanzania', 'created_by' => $admin->id])->id, 'name' => 'Inquiry customer', 'email' => 'inquiry@example.test', 'phone' => '255700000002']);
        $inquiry = \App\Project\Modules\System\Inquiries\Inquiry::create(['tourist_id' => $tourist->id, 'from_date' => '2026-10-01', 'to_date' => '2026-10-04', 'guests' => 2, 'assigned_to' => $admin->id]);
        $currency = \App\Project\Modules\Core\Currencies\Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => '$']);
        $status = \App\Project\Modules\System\Quotations\QuotationStatus::create(['name' => 'Open', 'color' => '#288479']);
        $quote = \App\Project\Modules\System\Quotations\Quotation::create(['created_by' => $admin->id, 'quotation_number' => 'TEST-QUOTE', 'quotation_date' => '2026-09-20', 'inquiry_id' => $inquiry->id, 'tourist_id' => $tourist->id, 'currency_id' => $currency->id, 'quotation_status_id' => $status->id, 'amount' => 120, 'vat_amount' => 0, 'exchange_rate' => 1, 'total_amount' => 120]);
        $this->getJson('/workspace/modules/inquiries')->assertOk()
            ->assertJsonPath('records.0.tourist.email', 'inquiry@example.test')
            ->assertJsonPath('records.0.assigned_to.username', $admin->username)
            ->assertJsonPath('records.0.quotations_count', 1)
            ->assertJsonPath('records.0.service_details_exists', false);
        $this->getJson('/workspace/modules/inquiries/'.$inquiry->uuid)->assertOk()
            ->assertJsonPath('details.inquiry.quotations.0.uuid', $quote->uuid)
            ->assertJsonPath('details.inquiry.quotations.0.currency.short_name', 'USD')
            ->assertJsonPath('details.inquiry.quotations.0.status.name', 'Open')
            ->assertJsonPath('actions.can_create_quotation', false);
        $this->postJson('/inquiries/change_status/'.$inquiry->uuid, ['new_status' => 1, 'comments' => 'Reviewed'])->assertOk();
        $this->getJson('/workspace/modules/inquiries/'.$inquiry->uuid)->assertOk()
            ->assertJsonPath('details.inquiry.comments', 'Reviewed');
    }
}
