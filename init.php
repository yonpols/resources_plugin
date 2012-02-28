<?php
    Resources\MainController::$mountPoint = YPFramework::getSetting('resources.mount_point', 'resources');

    //Set the route settings according to the mount point set by the application settings
    $route = new YPFObject;
    $route->match = '/'.YPFramework::getFileName(Resources\MainController::$mountPoint, '$path');
    $route->controller = 'resources\main';
    $route->action = 'get';
    YPFramework::setSetting('routes.resources_get', $route);
?>
