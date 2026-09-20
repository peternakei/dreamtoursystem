<?php

use App\Project\Modules\System\Faqs\ApiControllers\FaqController;
use Illuminate\Support\Facades\Route;

Route::controller(FaqController::class)->group(function () {
    Route::get('faqs', 'getFaqs')->name('faqs');
});
