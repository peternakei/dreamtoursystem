<?php

namespace App\Project\Modules\System\Splash\Services\Api;

use App\Project\Modules\System\Attachments\AttachmentType;
use App\Project\Modules\Core\AgeGroups\AgeGroup;
use App\Project\Modules\Core\AgeRanges\AgeRange;
use App\Project\Modules\Core\Countries\Country;
use App\Project\Modules\Core\Currencies\Currency;
use App\Project\Modules\Core\DurationTypes\DurationType;
use App\Project\Modules\Core\Genders\Gender;
use App\Project\Modules\Core\Locations\Location;
use App\Project\Modules\Core\PaymentModes\PaymentMode;
use App\Project\Modules\Core\Regions\Region;
use App\Project\Modules\System\Seasons\Season;
use App\Project\Modules\System\Seasons\ServiceClass;
use App\Project\Modules\System\Trips\Category;
use Illuminate\Support\Facades\DB;

class GetSplashFormAction
{
    public function handle()
    {
        $data = [
            'locations' => Location::select('id', 'name')->get(),
            'categories' => Category::select('id', 'name')->get(),
            'seasons' => Season::select('id', 'name')->get(),
            'searviceClasses' => ServiceClass::select('id', 'name')->get(),
            'ageGroups' => AgeGroup::select('id', 'name')->get(),
            'currencies' => Currency::select('id', 'name', 'short_name as code','uuid', DB::raw('(SELECT rate FROM exchange_rates WHERE exchange_rates.currency_id = currencies.id LIMIT 1) as rate'),'symbol')->get(),
            'genders' => Gender::select('id','name')->get(),
            'paymentModes' => PaymentMode::select('id','name')->get(),
            'attachmentTypes' =>  AttachmentType::select('id','name')->get(),
            'durationTypes' => DurationType::select('id','name')->get(),
            'countries' => Country::select('id','name','code')->get(),
            'regions' => Region::select('id','name')->get(),
            'ageRanges' => AgeRange::select('id','name')->get(),
        ];

        return response()->json([
            'status' => true,
            'code' => 100,
            'message' => 'Splash fetched successfully',
            'data' => $data
        ]);
    }
}
