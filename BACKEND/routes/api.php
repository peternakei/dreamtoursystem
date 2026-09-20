<?php

\Illuminate\Support\Facades\Route::name('api.')->group(function () {
foreach (\App\Project\_Src\Registry::routes('api') as $file) {
    require base_path($file);
}

});
