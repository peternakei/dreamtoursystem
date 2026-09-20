<?php

foreach (\App\Project\_Src\Registry::routes('web') as $file) {
    require base_path($file);
}

require app_path('Project/Workspace/Routes/web.php');
