<?php

use App\Project\Modules\System\Destinations\DestinationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:web'], function () {

    Route::prefix('destinations')->controller(DestinationController::class)->group(function () {
        Route::get('approved', 'active')->name('destinations.active');
        Route::get('inactive', 'inactive')->name('destinations.inactive');
        Route::post('change_status/{id}', 'changeStatus')->name('destinations.change_status');
        Route::post('upload_images/{id}', 'uploadImages')->name('destinations.upload_images');
        Route::get('image/{id}', 'image')->name('destinations.image');
        Route::get('delete_image/{id}', 'deleteImage')->name('destinations.delete_image');
        Route::delete('destroy_image/{destination}/{id}', 'destroyImage')->name('destinations.destroy_image');
        Route::post('assign_activity/{id}', 'assignActivity')->name('destinations.assign_activity');
        Route::post('create_fact/{id}', 'createFact')->name('destinations.create_fact');
        Route::get('fact/{id}', 'fact')->name('destinations.fact');
        Route::put('update_destination_fact/{destination}/{id}', 'updateFact')->name('destinations.update_destination_fact');
        Route::post('assign_category/{id}', 'assignCategory')->name('destinations.assign_category');
    });

    Route::resources([
        'destinations' => DestinationController::class
    ]);
});
