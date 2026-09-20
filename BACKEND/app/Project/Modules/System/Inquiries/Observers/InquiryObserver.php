<?php

namespace App\Project\Modules\System\Inquiries\Observers;

use App\Project\Modules\System\Inquiries\Inquiry;

class InquiryObserver
{
    /**
     * Handle the Inquiry "created" event.
     */
    public function created(Inquiry $inquiry): void
    {
        $inquiry->inquiry_code = 'INQ' . sprintf('%0000004d', $inquiry->id);
        $inquiry->save();
    }

    /**
     * Handle the Inquiry "updated" event.
     */
    public function updated(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "deleted" event.
     */
    public function deleted(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "restored" event.
     */
    public function restored(Inquiry $inquiry): void
    {
        //
    }

    /**
     * Handle the Inquiry "force deleted" event.
     */
    public function forceDeleted(Inquiry $inquiry): void
    {
        //
    }
}
