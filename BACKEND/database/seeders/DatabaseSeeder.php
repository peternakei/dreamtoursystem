<?php

namespace Database\Seeders;

use App\Project\Modules\Core\Menus\Seeders\LibraryMenuSeeder;
use App\Project\Modules\Core\Menus\Seeders\MenuSeeder;
use App\Project\Modules\Core\Permissions\Seeders\LibraryMenuPermissionSeeder;
use App\Project\Modules\Core\Permissions\Seeders\MenuPermissionSeeder;
use App\Project\Modules\Core\Permissions\Seeders\NormalPermissionSeeder;
use App\Project\Modules\Core\Roles\Seeders\RoleAssignPermissionSeeder;
use App\Project\Modules\Core\Roles\Seeders\RoleSeeder;
use App\Project\Modules\Core\SystemConfigurations\Seeders\SystemConfigurationTypesSeeder;
use App\Project\Modules\Core\Users\Seeders\AssignUserPermissionSeeder;
use App\Project\Modules\Core\Users\Seeders\UserSeeder;
use App\Project\Modules\System\Activities\Seeders\ActivitySeeder;
use App\Project\Modules\System\Attachments\Seeders\AttachmentTypeSeeder;
use App\Project\Modules\System\Bookings\Seeders\BookingSourceSeeder;
use App\Project\Modules\System\Bookings\Seeders\BookingStatusSeeder;
use App\Project\Modules\System\Bookings\Seeders\BookingTypeSeeder;
use App\Project\Modules\System\Addons\Seeders\AddonSeeder;
use App\Project\Modules\Core\AgeGroups\Seeders\AgeGroupSeeder;
use App\Project\Modules\Core\AgeRanges\Seeders\AgeRangeSeeder;
use App\Project\Modules\Core\Countries\Seeders\CountrySeeder;
use App\Project\Modules\Core\Currencies\Seeders\CurrencySeeder;
use App\Project\Modules\Core\DiscountTypes\Seeders\DiscountTypeSeeder;
use App\Project\Modules\Core\Districts\Seeders\DistrictSeeder;
use App\Project\Modules\Core\DurationTypes\Seeders\DurationTypeSeeder;
use App\Project\Modules\System\ExchangeRates\Seeders\ExchangeRateSeeder;
use App\Project\Modules\System\FaqCategory\Seeders\FaqCategorySeeder;
use App\Project\Modules\Core\Genders\Seeders\GenderSeeder;
use App\Project\Modules\Core\Locations\Seeders\LocationSeeder;
use App\Project\Modules\Core\PaymentModes\Seeders\PaymentModeSeeder;
use App\Project\Modules\System\Accommodations\Seeders\PublicSiteAccommodationSeeder;
use App\Project\Modules\Core\Regions\Seeders\RegionSeeder;
use App\Project\Modules\System\Destinations\Seeders\PublicSiteDestinationSeeder;
use App\Project\Modules\System\Invoices\Seeders\InvoiceStatusSeeder;
use App\Project\Modules\System\Inquiries\Seeders\InquiryStarterSeeder;
use App\Project\Modules\System\Quotations\Seeders\QuotationStatusSeeder;
use App\Project\Modules\System\Quotations\Seeders\TestQuotationSeeder;
use App\Project\Modules\System\Refunds\Seeders\RefundStatusSeeder;
use App\Project\Modules\System\Seasons\Seeders\SeasonSeeder;
use App\Project\Modules\System\Seasons\Seeders\ServiceClassSeeder;
use App\Project\Modules\System\Trips\Seeders\CategorySeeder;
use App\Project\Modules\System\Trips\Seeders\PublicSiteTripSeeder;
use App\Project\Modules\System\Trips\Seeders\TripSourceSeeder;
use App\Project\Modules\System\Trips\Seeders\TripStatusSeeder;
use App\Project\Modules\System\Trips\Seeders\TripTypeSeeder;
use App\Project\Modules\System\Bookings\Seeders\VehicleSeeder;
use App\Project\Modules\System\Bookings\Seeders\StayTypeSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MenuSeeder::class,
            LibraryMenuSeeder::class,
            MenuPermissionSeeder::class,
            LibraryMenuPermissionSeeder::class,
            NormalPermissionSeeder::class,
            RoleSeeder::class,
            RoleAssignPermissionSeeder::class,
            AssignUserPermissionSeeder::class,
            AttachmentTypeSeeder::class,
            BookingSourceSeeder::class,
            BookingStatusSeeder::class,
            BookingTypeSeeder::class,
            AddonSeeder::class,
            AgeGroupSeeder::class,
            AgeRangeSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            ExchangeRateSeeder::class,
            DiscountTypeSeeder::class,
            RegionSeeder::class,
            DistrictSeeder::class,
            DurationTypeSeeder::class,
            GenderSeeder::class,
            LocationSeeder::class,
            PaymentModeSeeder::class,
            InvoiceStatusSeeder::class,
            QuotationStatusSeeder::class,
            RefundStatusSeeder::class,
            SeasonSeeder::class,
            ServiceClassSeeder::class,
            CategorySeeder::class,
            TripSourceSeeder::class,
            TripStatusSeeder::class,
            TripTypeSeeder::class,
            ActivitySeeder::class,
            FaqCategorySeeder::class,
            // InquiryStarterSeeder::class,
            StayTypeSeeder::class,
            VehicleSeeder::class,
            // Public-site library (depends on Categories, Locations, Regions,
            // StayTypes, Currencies, AgeGroups, TripType/Status above).
            PublicSiteDestinationSeeder::class,
            PublicSiteAccommodationSeeder::class,
            PublicSiteTripSeeder::class,
            // Demo quotation built on top of the seeded library.
            TestQuotationSeeder::class,
        ]);
    }
}
