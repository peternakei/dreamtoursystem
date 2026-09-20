<?php

use App\Project\Modules\System\Likes\ApiControllers\LikeController;
use Illuminate\Support\Facades\Route;

Route::controller(LikeController::class)->group(function () {
    Route::post('toggle_like', 'toggleLike')->name('like.toggle')->middleware('auth:sanctum');
    Route::get('user/likes', 'getUserLikes')->name('like.get')->middleware('auth:sanctum');
    Route::get('user/check-like-{type}', 'checkLikeStatus')->name('like.check')->middleware('auth:sanctum');
});
