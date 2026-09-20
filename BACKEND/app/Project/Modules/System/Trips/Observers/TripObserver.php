<?php

namespace App\Project\Modules\System\Trips\Observers;

use App\Project\Modules\System\Trips\Trip;
use Illuminate\Support\Str;

class TripObserver
{
    /**
     * Handle the Trip "created" event.
     */
    public function created(Trip $trip): void
    {
        $trip->trip_code = 'TRC' . sprintf('%0000004d', $trip->id);
        $trip->slug = Str::slug($trip->name);
        $trip->save();
    }

    /**
     * Handle the Trip "updated" event.
     */
    public function updated(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "deleted" event.
     */
    public function deleted(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "restored" event.
     */
    public function restored(Trip $trip): void
    {
        //
    }

    /**
     * Handle the Trip "force deleted" event.
     */
    public function forceDeleted(Trip $trip): void
    {
        //
    }
}
