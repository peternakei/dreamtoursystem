<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content Library Entity Registry
    |--------------------------------------------------------------------------
    |
    | Maps URL slug -> Eloquent model class for library entities. Each entity
    | listed here uses the HasLibraryMedia trait and is editable through the
    | shared LibraryMediaController endpoints.
    |
    | Add new entities (Accommodation, Vehicle, Activity, Theme, ...) here as
    | their slices land — no controller changes required.
    |
    */

    'entities' => [
        'destinations' => \App\Project\Modules\System\Destinations\Destination::class,
        'vehicles' => \App\Project\Modules\System\Vehicles\Vehicle::class,
        'accommodations' => \App\Project\Modules\System\Accommodations\Accommodation::class,
        'activities' => \App\Project\Modules\System\Activities\Activity::class,
        'categories' => \App\Project\Modules\System\Trips\Category::class,
    ],

    'roles' => [
        'gallery',
        'cover',
        'video',
    ],

    'attachment_type_name' => 'library_media',
    'attachment_type_color' => '#10b981',

    'max_image_mb' => 15,
    'max_video_mb' => 100,

    'allowed_image_mimes' => 'jpeg,jpg,png,webp,gif,bmp',
    'allowed_video_mimes' => 'mp4,mov,webm,m4v',
];
