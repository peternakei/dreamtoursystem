<?php

namespace App\Project\Modules\System\Trips\Services;

use App\Project\Modules\System\Seasons\Budget;
use App\Project\Modules\System\Trips\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaveTripBudgetMatrixFormAction
{
    public function handle(Request $request, string $id): array
    {
        DB::beginTransaction();

        $trip = Trip::where('uuid', $id)->first();
        $savedCount = 0;

        foreach (($request->prices ?? []) as $seasonId => $classPrices) {
            if (!is_array($classPrices)) {
                continue;
            }

            foreach ($classPrices as $classId => $price) {
                if ($price === null || $price === '') {
                    continue;
                }

                if (!is_numeric($price) || (float) $price < 0) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Each budget price must be a valid positive number.'];
                }

                $exists = Budget::where([
                    'trip_id' => $trip->id,
                    'season_id' => $seasonId,
                    'service_class_id' => $classId,
                    'quantity' => $request->quantity,
                ])->get();

                if (count($exists) > 0) {
                    $suspend = Budget::where([
                        'trip_id' => $trip->id,
                        'season_id' => $seasonId,
                        'service_class_id' => $classId,
                        'quantity' => $request->quantity,
                    ])->update(['is_active' => false]);

                    if (!$suspend) {
                        DB::rollBack();
                        return ['status' => false, 'message' => 'Failed to suspend an existing budget row.'];
                    }
                }

                $save = Budget::create([
                    'trip_id' => $trip->id,
                    'season_id' => $seasonId,
                    'service_class_id' => $classId,
                    'currency_id' => $request->currency,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'created_by' => Auth::user()->id,
                ]);

                if (!$save) {
                    DB::rollBack();
                    return ['status' => false, 'message' => 'Failed to save trip budget details.'];
                }

                $savedCount++;
            }
        }

        if ($savedCount === 0) {
            DB::rollBack();
            return ['status' => false, 'message' => 'Enter at least one budget value before saving.'];
        }

        DB::commit();

        return [
            'status' => true,
            'message' => $savedCount . ' budget row(s) saved successfully.',
        ];
    }
}
