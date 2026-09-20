<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('db:boot', function () {
    $this->call("migrate");
    $this->call("db:seed");
});

Artisan::command('db:reboot', function () {
    $this->call("migrate:fresh");
});
