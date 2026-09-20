<?php

use App\Project\Modules\System\Quotations\ApiControllers\PublicQuoteController;
use Illuminate\Support\Facades\Route;

/*
| Stateless public quote API. Token is the version's public_token,
| identical to the value used by the web routes registered as
| public.itinerary.* in routes/web/system/quotation/quotation.php.
*/

Route::prefix('public/quote')->controller(PublicQuoteController::class)->group(function () {
    Route::get('{token}', 'show')->name('public.quote.show');
    Route::post('{token}/accept', 'accept')->name('public.quote.accept');
    Route::post('{token}/request-changes', 'requestChanges')->name('public.quote.request_changes');
});
