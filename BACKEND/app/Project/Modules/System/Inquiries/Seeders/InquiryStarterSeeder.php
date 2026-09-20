<?php

namespace App\Project\Modules\System\Inquiries\Seeders;

use App\Project\Modules\Core\Users\User;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\System\Destinations\Destination;
use App\Project\Modules\System\Inquiries\Inquiry;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Tourists\Tourist;
use App\Project\Modules\System\Trips\TripType;
use Illuminate\Database\Seeder;

class InquiryStarterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userId = User::query()->value('id');
        $countryId = Country::query()->value('id');
        $tripTypeId = TripType::query()->value('id');
        $serviceClassId = ServiceClass::query()->value('id');
        $destinationIds = Destination::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->limit(3)
            ->pluck('id')
            ->values()
            ->all();

        if (!$countryId) {
            return;
        }

        $tourist = Tourist::query()->firstOrCreate(
            ['phone' => '+255700000112'],
            [
                'name' => 'Emanuel Sige',
                'email' => 'emanuel.sige@example.com',
                'country_id' => $countryId,
                'address' => 'Arusha, Tanzania',
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        Inquiry::query()->updateOrCreate(
            ['inquiry_code' => 'INQ-SEED-0001'],
            [
                'name' => 'Northern Circuit Safari Starter',
                'tourist_id' => $tourist->id,
                'description' => 'Sample inquiry seeded for early-stage quotation work. Guest wants a polished northern circuit safari proposal with strong visuals and clear inclusions.',
                'tour_title' => '6 Day Northern Circuit Safari',
                'from_date' => now()->addDays(45)->toDateString(),
                'to_date' => now()->addDays(50)->toDateString(),
                'trip_type_id' => $tripTypeId,
                'destinations' => $destinationIds,
                'locations' => [],
                'service_class_id' => $serviceClassId,
                'guests' => 2,
                'budget' => '4500-6000 USD',
                'status' => 'approved',
                'source' => 'seeded',
                'communication_language' => 'English',
                'received_at' => now(),
                'request_reference' => '2026-START-0001',
                'client_message' => 'Please prepare a professional safari quote with accommodation suggestions, day-by-day experiences, and a client-ready PDF we can share immediately.',
                'is_approved' => true,
                'comments' => 'Seeded starter inquiry for quotation testing.',
                'user_id' => $userId,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        Inquiry::query()->updateOrCreate(
            ['inquiry_code' => 'INQ-SEED-0002'],
            [
                'name' => 'Family Safari Draft',
                'tourist_id' => $tourist->id,
                'description' => 'Secondary seeded inquiry left pending so the team can test both pre-approval and quotation-ready states.',
                'tour_title' => 'Family Safari Idea',
                'from_date' => now()->addDays(70)->toDateString(),
                'to_date' => now()->addDays(74)->toDateString(),
                'trip_type_id' => $tripTypeId,
                'destinations' => array_slice($destinationIds, 0, 2),
                'locations' => [],
                'service_class_id' => $serviceClassId,
                'guests' => 4,
                'budget' => '7000-8500 USD',
                'status' => 'pending',
                'source' => 'seeded',
                'communication_language' => 'English',
                'received_at' => now(),
                'request_reference' => '2026-START-0002',
                'client_message' => 'Need a family-friendly version with gentle pacing and room for optional add-ons.',
                'is_approved' => false,
                'comments' => 'Seeded pending inquiry for workflow testing.',
                'user_id' => $userId,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );
    }
}
