<?php

use App\Project\Modules\System\Trips\TripController;
use App\Project\Modules\System\Trips\TripPlannerController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('trips/{id}/planner', [TripPlannerController::class, 'edit'])->name('trips.planner.edit');
    Route::put('trips/{id}/planner', [TripPlannerController::class, 'update'])->name('trips.planner.update');

    Route::prefix('trips')->controller(TripController::class)->group(function () {
        Route::get('approved', 'approved')->name('trips.approved');
        Route::get('pending', 'pending')->name('trips.pending');
        Route::get('cancelled', 'cancelled')->name('trips.cancelled');
        Route::get('completed', 'completed')->name('trips.completed');
        Route::post('change_status/{id}', 'changeStatus')->name('trips.change_status');
        Route::post('publish/{id}', 'publish')->name('trips.publish');
        Route::post('apply_to_inquiry/{id}', 'applyToInquiry')->name('trips.apply_to_inquiry');
        Route::post('create_addon/{id}', 'createAddon')->name('trips.create_addon');
        Route::post('create_category/{id}', 'createCategory')->name('trips.create_category');
        Route::post('create_destination/{id}', 'createDestination')->name('trips.create_destination');
        Route::post('create_category_activity/{id}', 'createCategoryActivity')->name('trips.create_category_activity');
        Route::post('create_point/{id}', 'createPoint')->name('trips.create_point');
        Route::post('create_group/{id}', 'createGroup')->name('trips.create_group');
        Route::post('create_group_camp/{id}', 'createGroupCamp')->name('trips.create_group_camp');
        Route::post('upload_banner/{id}', 'uploadBanner')->name('trips.upload_banner');
        Route::post('create_price/{id}', 'createPrice')->name('trips.create_price');
        Route::post('create_budget_matrix/{id}', 'createBudgetMatrix')->name('trips.create_budget_matrix');
        Route::get('get_point/{id}', 'getPoint')->name('trips.get_point');
        Route::get('get_addon/{id}', 'getAddon')->name('trips.get_addon');
        Route::get('edit_point/{id}', 'editTripPoint')->name('trips.edit_point');
        Route::put('update_point/{id}/{trip}', 'updatePoint')->name('trips.update_point');
        Route::delete('delete_point/{id}', 'deletePoint')->name('trips.delete_point');
        Route::delete('delete_addon/{id}', 'deleteAddon')->name('trips.delete_addon');
    });

    Route::resources([
        'trips' => TripController::class
    ]);
});
