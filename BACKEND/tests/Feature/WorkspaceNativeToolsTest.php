<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Accommodations\Accommodation;
use App\Project\Modules\System\Activities\Activity;
use App\Project\Modules\System\Attachments\Attachment;
use App\Project\Modules\System\BankDetails\BankDetail;
use App\Project\Modules\System\Banks\Bank;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Destinations\DestinationFact;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Trip;
use App\Project\Modules\System\Trips\TripType;
use App\Project\Modules\System\Vehicles\Vehicle;
use App\Project\Workspace\Services\PageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkspaceNativeToolsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['username' => 'native@example.test', 'password' => bcrypt('fixture-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $profile = SystemUser::create(['name' => 'Native staff', 'email' => 'native@example.test', 'phone' => '255700000411', 'created_by' => $this->admin->id]);
        $this->admin->update(['profile_id' => $profile->id]);
        $this->admin->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'uuid' => (string) Str::uuid(), 'created_by' => $this->admin->id]));
        foreach (['edit-destinations', 'edit-countries', 'edit-trips', 'edit-roles', 'delete-vehicles'] as $name) {
            $this->admin->givePermissionTo(Permission::create(['name' => $name, 'guard_name' => 'web', 'uuid' => (string) Str::uuid(), 'created_by' => $this->admin->id]));
        }
        $this->actingAs($this->admin)->withHeader('X-Safari-Workspace', '1');
    }

    private function destination(): Destination
    {
        return Destination::create(['name' => 'Native park', 'latitude' => -3.5, 'longitude' => 35.8, 'description' => 'Park description', 'created_by' => $this->admin->id]);
    }

    public function test_library_records_supply_native_media_and_edit_forms(): void
    {
        $vehicle = Vehicle::create(['name' => 'Native cruiser', 'capacity' => '6 guests', 'created_by' => $this->admin->id]);
        $accommodation = Accommodation::create(['name' => 'Native lodge', 'created_by' => $this->admin->id]);
        foreach (['vehicles' => $vehicle, 'accommodations' => $accommodation] as $slug => $record) {
            $data = $this->getJson('/workspace/modules/'.$slug.'/'.$record->uuid)->assertOk()->assertJsonPath('media.uuid', $record->uuid)->json();
            $this->assertTrue(collect($data['forms'])->contains(fn ($f) => $f['method'] === 'PUT' && $f['action'] === '/'.$slug.'/'.$record->uuid));
            $this->assertTrue(collect($data['forms'])->contains(fn ($f) => $f['action'] === '/library/'.$slug.'/'.$record->uuid.'/description'));
        }
        $this->patchJson('/library/vehicles/'.$vehicle->uuid.'/description', ['description' => '<h2>Safari vehicle</h2><p>Comfortable seats</p>'])->assertOk();
        $this->assertStringContainsString('<h2>', $vehicle->fresh()->description);
        $this->putJson('/vehicles/'.$vehicle->uuid, ['name' => $vehicle->name, 'capacity' => '7 guests'])->assertOk();
        $this->assertSame('7 guests', $vehicle->fresh()->capacity);
        $other = Vehicle::create(['name' => 'Another cruiser', 'created_by' => $this->admin->id]);
        $this->putJson('/vehicles/'.$vehicle->uuid, ['name' => $other->name])->assertUnprocessable()->assertJsonValidationErrors('name');
    }

    public function test_media_upload_rename_reorder_and_remove_use_existing_routes(): void
    {
        Storage::fake('attachments');
        $destination = $this->destination();
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
        $uploaded = $this->post('/library/destinations/'.$destination->uuid.'/media', ['role' => 'gallery', 'files' => [UploadedFile::fake()->createWithContent('park.png', $png)]], ['Accept' => 'application/json'])->assertOk()->json('data.0');
        $media = Attachment::findOrFail($uploaded['id']);
        Storage::disk('attachments')->assertExists('uploads/'.$media->name);
        $this->patchJson('/library/media/'.$media->id, ['title' => 'Park cover'])->assertOk();
        $this->postJson('/library/destinations/'.$destination->uuid.'/media/reorder', ['role' => 'gallery', 'order' => [$media->id]])->assertOk();
        $this->getJson('/workspace/modules/destinations/'.$destination->uuid)->assertOk()->assertJsonPath('media.images.0.title', 'Park cover')->assertJsonPath('media.images.0.role', 'gallery');
        $this->deleteJson('/library/media/'.$media->id)->assertOk();
        $this->assertSoftDeleted('attachments', ['id' => $media->id]);
        $this->post('/library/destinations/'.$destination->uuid.'/media', ['role' => 'gallery', 'files' => [UploadedFile::fake()->createWithContent('bad.txt', 'not an image')]], ['Accept' => 'application/json'])->assertUnprocessable();
    }

    public function test_destination_fact_forms_and_repeatable_assignments_are_native(): void
    {
        $destination = $this->destination();
        $this->postJson('/destinations/create_fact/'.$destination->uuid, ['fact' => 'Wildlife', 'sub_fact' => 'Lions', 'description' => 'Protected habitat'])->assertOk();
        $fact = DestinationFact::firstOrFail();
        $page = $this->getJson('/workspace/modules/destinations/'.$destination->uuid)->assertOk()->json();
        $section = collect($page['sections'])->firstWhere('title', 'Facts');
        $form = $section['rows'][0]['_forms'][0];
        $this->assertSame('/destinations/update_destination_fact/'.$destination->uuid.'/'.$fact->uuid, $form['action']);
        $this->putJson($form['action'], ['fact' => 'Wildlife', 'sub_fact' => 'Elephants', 'description' => 'Updated habitat'])->assertOk();
        $this->assertDatabaseHas('destination_facts', ['id' => $fact->id, 'sub_fact' => 'Elephants']);
        $assign = collect($page['forms'])->first(fn ($f) => str_contains($f['action'], 'assign_activity'));
        $this->assertCount(1, $assign['repeaters']);
        $this->assertSame('activity[]', $assign['repeaters'][0]['fields'][0]['name']);
        $a = Activity::create(['name' => 'Hike', 'description' => 'Walk', 'created_by' => $this->admin->id]);
        $b = Activity::create(['name' => 'Birdwatching', 'description' => 'Birds', 'created_by' => $this->admin->id]);
        $this->postJson($assign['action'], ['activity' => [$a->id, $b->id], 'description' => ['Walk trail', 'See birds']])->assertOk();
        $this->assertSame(2, $destination->activities()->count());
    }

    public function test_configuration_edit_templates_preserve_values_and_role_assignments(): void
    {
        $country = Country::create(['name' => 'Test country', 'code' => 'TC', 'created_by' => $this->admin->id]);
        $page = $this->getJson('/workspace/modules/countries/'.$country->uuid)->assertOk()->json();
        $form = collect($page['forms'])->firstWhere('title', 'Edit details');
        $this->assertSame('TC', collect($form['fields'])->firstWhere('name', 'code')['value']);
        $this->putJson($form['action'], ['name' => 'Updated country', 'code' => 'UC'])->assertOk();
        $this->assertSame('UC', $country->fresh()->code);
        $role = Role::create(['name' => 'Review role', 'guard_name' => 'web', 'uuid' => '6aae3159bc3d8', 'created_by' => $this->admin->id]);
        $permission = Permission::first();
        $role->givePermissionTo($permission);
        $page = $this->getJson('/workspace/modules/roles/'.$role->uuid)->assertOk()->json();
        $form = collect($page['forms'])->firstWhere('title', 'Edit details');
        $field = collect($form['fields'])->firstWhere('name', 'permission[]');
        $this->assertContains($permission->id, $field['value']);
        $this->assertTrue($field['multiple']);
    }

    public function test_trip_itinerary_and_related_point_actions_stay_in_workspace(): void
    {
        $type = TripType::create(['name' => 'Private', 'color' => '#288479']);
        $trip = Trip::create(['trip_type_id' => $type->id, 'name' => 'Native safari', 'from_date' => '2026-10-01', 'to_date' => '2026-10-02', 'created_by' => $this->admin->id]);
        $destination = $this->destination();
        $destination->update(['is_active' => false]);
        $days = [['title' => 'Arrival', 'destination_id' => $destination->id, 'nights' => 1, 'breakfast' => true, 'activity_lines' => "Walking\nBird watching"], ['title' => 'Return', 'nights' => 0, 'activity_lines' => '']];
        $this->putJson('/trips/'.$trip->uuid.'/planner', ['days' => $days])->assertOk()->assertJsonPath('status', true);
        $this->assertSame(2, $trip->tripDays()->count());
        $this->assertSame(2, $trip->tripDays()->first()->activities()->count());
        $this->assertSame(1, (int) $trip->fresh()->duration_nights);
        $point = $trip->points()->create(['title' => 'Meet guide', 'description' => 'Airport', 'created_by' => $this->admin->id]);
        $group = $trip->groups()->create(['group' => 'Family A', 'size' => 4, 'color' => '#288479', 'departure_date' => '2026-10-01', 'days' => 2, 'description' => 'Family', 'created_by' => $this->admin->id]);
        $page = $this->getJson('/workspace/modules/trips/'.$trip->uuid)->assertOk()->json();
        $this->assertSame('Arrival', $page['planner']['seedRows'][0]['title']);
        $this->assertContains($destination->id, array_column($page['planner']['destinations'], 'id'));
        $groups = collect($page['forms'])->flatMap(fn ($f) => $f['fields'])->first(fn ($field) => $field['name'] === 'group' && $field['type'] === 'select');
        $this->assertSame([['value' => (string) $group->id, 'label' => 'Family A']], $groups['options']);
        $section = collect($page['sections'])->firstWhere('title', 'Points');
        $edit = $section['rows'][0]['_forms'][0];
        $this->putJson($edit['action'], ['title' => 'Meet driver', 'description' => 'Hotel'])->assertOk();
        $this->assertSame('Meet driver', $point->fresh()->title);
        $this->deleteJson($section['rows'][0]['_forms'][1]['action'])->assertOk();
        $this->assertSoftDeleted('trip_points', ['id' => $point->id]);
        $this->postJson('/trips/create_group_camp/'.$trip->uuid, ['group' => $group->id, 'camp' => 'Riverside', 'day' => 1, 'description' => 'Overnight camp'])->assertOk()->assertJsonPath('status', true);
        $this->assertSame(1, $trip->groupCamps()->count());
        $season = Season::create(['name' => 'High season', 'created_by' => $this->admin->id]);
        $class = ServiceClass::create(['name' => 'Comfort']);
        $currency = Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => 'USD', 'created_by' => $this->admin->id]);
        $page = $this->getJson('/workspace/modules/trips/'.$trip->uuid)->assertOk()->json();
        $matrix = collect($page['forms'])->first(fn ($f) => str_contains($f['action'], 'create_budget_matrix'));
        $this->assertSame('High season / Comfort', collect($matrix['fields'])->firstWhere('name', 'prices['.$season->id.']['.$class->id.']')['label']);
        $this->postJson($matrix['action'], ['currency' => $currency->id, 'quantity' => 2, 'prices' => [$season->id => [$class->id => 245.50]]])->assertOk()->assertJsonPath('status', true);
        $this->assertDatabaseHas('budgets', ['trip_id' => $trip->id, 'season_id' => $season->id, 'service_class_id' => $class->id, 'price' => 245.5]);
    }

    public function test_bank_account_editor_respects_the_existing_hyphenated_permission(): void
    {
        $bank = Bank::create(['name' => 'Test bank', 'created_by' => $this->admin->id]);
        $currency = Currency::create(['name' => 'Dollar', 'short_name' => 'USD', 'symbol' => 'USD']);
        $account = BankDetail::create(['bank_id' => $bank->id, 'currency_id' => $currency->id, 'account_name' => 'Test account', 'account_number' => '001', 'created_by' => $this->admin->id]);
        $page = $this->getJson('/workspace/modules/bank_details/'.$account->uuid)->assertOk()->json();
        $this->assertFalse(collect($page['forms'])->contains('title', 'Edit details'));
        $this->admin->givePermissionTo(Permission::create(['name' => 'edit-bank-details', 'guard_name' => 'web', 'uuid' => (string) Str::uuid(), 'created_by' => $this->admin->id]));
        $page = $this->getJson('/workspace/modules/bank_details/'.$account->uuid)->assertOk()->json();
        $form = collect($page['forms'])->firstWhere('title', 'Edit details');
        $this->assertSame($bank->id, collect($form['fields'])->firstWhere('name', 'bank')['value']);
        $this->putJson($form['action'], ['bank' => $bank->id, 'currency' => $currency->id, 'account_name' => 'Updated account', 'account_number' => '002'])->assertOk()->assertJsonPath('status', true);
        $this->assertSame('002', $account->fresh()->account_number);
    }

    public function test_form_parser_retains_repeaters_checkbox_ids_and_rich_text(): void
    {
        $html = '<form method="POST" action="/trips"><input type="hidden" name="description"><table><tbody><tr><td><select name="destination[]"><option value="1">Park</option></select></td><td><input name="description[]"></td></tr></tbody></table></form><form action="/roles" method="POST"><table><tbody><tr><td>Read</td><td><input type="checkbox" name="permission[]" value="7" checked></td></tr><tr><td>Edit</td><td><input type="checkbox" name="permission[]" value="8"></td></tr></tbody></table></form>';
        $forms = PageService::forms($html, '/trips');
        $this->assertSame('richtext', $forms[0]['fields'][0]['type']);
        $this->assertCount(2, $forms[0]['repeaters'][0]['fields']);
        $this->assertSame(['7'], $forms[1]['fields'][0]['value']);
        $this->assertSame('Read', $forms[1]['fields'][0]['options'][0]['label']);
    }

    public function test_local_module_navigation_redirects_but_writes_keep_laravel_validation(): void
    {
        $this->app['env'] = 'local';
        $vehicle = Vehicle::create(['name' => 'Navigation cruiser', 'capacity' => '6', 'created_by' => $this->admin->id]);
        foreach (PageService::modules() as $slug => $definition) {
            $this->get('/'.$slug)->assertRedirect('http://127.0.0.1:5174/'.$slug.'/list');
            $this->get('/'.$slug.'/'.$vehicle->uuid)->assertRedirect('http://127.0.0.1:5174/'.$slug.'/'.$vehicle->uuid.'/details');
        }
        $this->get('/roles/6aae3159bc3d8')->assertRedirect('http://127.0.0.1:5174/roles/6aae3159bc3d8/details');
        $this->get('/trips/'.$vehicle->uuid.'/planner')->assertRedirect('http://127.0.0.1:5174/trips/'.$vehicle->uuid.'/details?tab=itinerary');
        $this->app['env'] = 'testing';
        $this->putJson('/vehicles/'.$vehicle->uuid, [])->assertUnprocessable();
        $this->getJson('/workspace/modules/vehicles/'.$vehicle->uuid)->assertOk();
        auth()->logout();
        $this->getJson('/workspace/modules/vehicles/'.$vehicle->uuid)->assertUnauthorized();
    }
}
