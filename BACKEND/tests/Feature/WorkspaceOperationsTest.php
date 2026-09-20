<?php

namespace Tests\Feature;

use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\Core\PaymentModes\PaymentMode;
use App\Project\Modules\Core\Users\SystemUser;
use App\Project\Modules\Core\Users\User;
use App\Project\Modules\System\Bookings\Booking;
use App\Project\Modules\System\Bookings\BookingStatus;
use App\Project\Modules\System\Invoices\Invoice;
use App\Project\Modules\System\Invoices\InvoiceStatus;
use App\Project\Modules\System\Receipts\Receipt;
use App\Project\Modules\System\Tourists\Tourist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkspaceOperationsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Tourist $tourist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['username' => 'ops@example.test', 'password' => bcrypt('fixture-password'), 'profile' => 'SystemUser', 'profile_id' => 0]);
        $profile = SystemUser::create(['name' => 'Workspace staff', 'email' => 'ops@example.test', 'phone' => '255700000321', 'created_by' => $this->admin->id]);
        $this->admin->update(['profile_id' => $profile->id]);
        $this->admin->assignRole(Role::create(['name' => 'SuperAdmin', 'guard_name' => 'web', 'created_by' => $this->admin->id, 'uuid' => (string) Str::uuid()]));
        foreach (['create-tourists', 'edit-tourists', 'delete-tourists'] as $name) {
            $this->admin->givePermissionTo(Permission::create(['name' => $name, 'guard_name' => 'web', 'created_by' => $this->admin->id, 'uuid' => (string) Str::uuid()]));
        }
        $country = Country::create(['name' => 'Tanzania', 'created_by' => $this->admin->id]);
        $gender = Gender::create(['name' => 'Female', 'color' => '#288479']);
        $this->tourist = Tourist::create(['name' => 'Operations customer', 'email' => 'customer@example.test', 'phone' => '255700000322', 'country_id' => $country->id, 'gender_id' => $gender->id]);
        $this->actingAs($this->admin);
    }

    public function test_financial_relationships_and_customer_history_stay_in_workspace(): void
    {
        $currency = Currency::create(['name' => 'US Dollar', 'short_name' => 'USD', 'symbol' => '$']);
        $status = BookingStatus::create(['name' => 'Reserved', 'color' => '#288479']);
        $booking = Booking::create(['remarks' => '', 'tourist_id' => $this->tourist->id, 'booking_number' => 'BK-FIXTURE', 'booking_date' => '2026-11-01', 'booking_status_id' => $status->id, 'guest_count' => 2, 'currency_id' => $currency->id, 'amount' => 100, 'vat_amount' => 18, 'total_amount' => 118]);
        $invoice = Invoice::create(['remarks' => '', 'tourist_id' => $this->tourist->id, 'booking_id' => $booking->id, 'invoice_number' => 'INV-FIXTURE', 'invoice_date' => '2026-10-01', 'amount' => 100, 'vat_amount' => 18, 'total_amount' => 118, 'currency_id' => $currency->id, 'exchange_rate' => 1, 'invoice_status_id' => InvoiceStatus::create(['name' => 'Pending', 'color' => '#FFA319'])->id]);
        $receipt = Receipt::create(['remarks' => '', 'created_by' => $this->admin->id, 'payment_mode_id' => PaymentMode::create(['name' => 'Cash', 'color' => '#FFA319'])->id, 'invoice_id' => $invoice->id, 'reference_number' => 'REC-FIXTURE', 'receipt_date' => '2026-10-02', 'amount' => 50, 'currency_id' => $currency->id]);
        $this->getJson('/workspace/modules/bookings/'.$booking->uuid)->assertOk()->assertJsonPath('details.record.tourist.name', 'Operations customer')->assertJsonPath('sections.0.rows.0.uuid', $invoice->uuid)->assertJsonPath('sections.1.rows.0.uuid', $receipt->uuid)->assertJsonPath('links.0.to', '/tourists/'.$this->tourist->uuid.'/details')->assertJsonCount(0, 'forms');
        $this->getJson('/workspace/modules/invoices/'.$invoice->uuid)->assertOk()->assertJsonPath('sections.0.rows.0.uuid', $receipt->uuid)->assertJsonPath('links.0.to', '/bookings/'.$booking->uuid.'/details');
        $this->getJson('/workspace/modules/receipts/'.$receipt->uuid)->assertOk()->assertJsonPath('links.0.to', '/invoices/'.$invoice->uuid.'/details');
        $this->getJson('/workspace/modules/tourists/'.$this->tourist->uuid)->assertOk()->assertJsonPath('sections.0.rows.0.uuid', $booking->uuid);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('receipts', 1);
    }

    public function test_tourist_forms_are_prefilled_and_use_existing_validated_actions(): void
    {
        $response = $this->getJson('/workspace/modules/tourists/'.$this->tourist->uuid)->assertOk();
        $edit = collect($response->json('forms'))->firstWhere('title', 'Edit tourist');
        $this->assertSame('/tourists/'.$this->tourist->uuid, $edit['action']);
        $this->assertSame($this->tourist->country_id, collect($edit['fields'])->firstWhere('name', 'country')['value']);
        $this->withHeader('X-Safari-Workspace', '1')->putJson($edit['action'], [])->assertUnprocessable();
        $this->putJson($edit['action'], ['name' => 'Updated customer', 'email' => $this->tourist->email, 'phone' => $this->tourist->phone, 'gender' => $this->tourist->gender_id, 'country' => $this->tourist->country_id, 'address' => 'Arusha'])->assertOk()->assertJsonPath('status', true);
        $this->postJson('/tourists/change_status/'.$this->tourist->uuid, ['new_status' => 2])->assertOk();
        $this->assertDatabaseHas('tourists', ['id' => $this->tourist->id, 'name' => 'Updated customer', 'address' => 'Arusha', 'is_active' => false]);
        $this->getJson('/workspace/modules/tourists')->assertOk()->assertJsonPath('forms.0.action', '/tourists')->assertJsonPath('records.0.country.name', 'Tanzania');
    }

    public function test_users_list_uses_system_user_login_relationship(): void
    {
        $this->getJson('/workspace/modules/users')->assertOk()->assertJsonPath('records.0.name', 'Workspace staff')->assertJsonPath('records.0.login.username', 'ops@example.test')->assertJsonPath('records.0.login.roles.0.name', 'SuperAdmin')->assertJsonMissingPath('records.0.login.password');
    }

    public function test_local_links_redirect_without_intercepting_data_requests(): void
    {
        $this->app['env'] = 'local';
        foreach (['bookings', 'tourists', 'invoices', 'receipts'] as $slug) {
            $this->get('/'.$slug)->assertRedirect('http://127.0.0.1:5174/'.$slug.'/list');
            $this->get('/'.$slug.'/'.$this->tourist->uuid)->assertRedirect('http://127.0.0.1:5174/'.$slug.'/'.$this->tourist->uuid.'/details');
            $this->getJson('/workspace/modules/'.$slug)->assertOk();
        }
        $this->getJson('/workspace/modules/tourists/'.Str::uuid())->assertNotFound();
        $this->app['env'] = 'production';
        $this->get('/tourists')->assertOk();
        auth()->logout();
        $this->getJson('/workspace/modules/bookings')->assertUnauthorized();
    }
}
