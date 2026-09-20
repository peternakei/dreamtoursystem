<?php

namespace App\Project\Modules\System\Tourists\Observers;

use App\Project\Modules\System\Tourists\Tourist;

class TouristObserver
{
    /**
     * Handle the Tourist "created" event.
     */
    public function created(Tourist $tourist): void
    {
        $tourist->tourist_number = 'VTN' . sprintf('%0000004d', $tourist->id);
        $tourist->save();
    }

    /**
     * Handle the Tourist "updated" event.
     */
    public function updated(Tourist $tourist): void
    {
        //
    }

    /**
     * Handle the Tourist "deleted" event.
     */
    public function deleted(Tourist $tourist): void
    {
        //
    }

    /**
     * Handle the Tourist "restored" event.
     */
    public function restored(Tourist $tourist): void
    {
        //
    }

    /**
     * Handle the Tourist "force deleted" event.
     */
    public function forceDeleted(Tourist $tourist): void
    {
        //
    }
}
