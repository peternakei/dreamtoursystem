<?php

return [
    // All other defaults continue to come from Spatie's installed package config.
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION', env('LOGS_DB_DATABASE') ? 'logs' : null),
    'activity_model' => App\Project\Modules\Core\Logs\Activity::class,
];
