<?php
    function image_tag($imageName, $extraParameters = array()) {
        $parameters = '';
        foreach($extraParameters as $param=>$value)
            $parameters .= sprintf('%s="%s" ', $param, htmlentities ($value, ENT_COMPAT));

        return sprintf('<img border="0" src="%s/%s/%s" %s/>', YPFramework::getSetting('application.url'),
            Resources\MainController::$mountPoint, $imageName, $parameters);
    }

    function javascript_tag($jsName) {
        return sprintf('<script type="text/javascript" src="%s/%s/%s"></script>',
            YPFramework::getSetting('application.url'), Resources\MainController::$mountPoint, $jsName);
    }

    function stylesheet_tag($cssName) {
        return sprintf('<link rel="stylesheet" type="text/css" href="%s/%s/%s" />',
            YPFramework::getSetting('application.url'), Resources\MainController::$mountPoint, $cssName);
    }

    function resource_url($resourceName) {
        return sprintf('%s/%s/%s', YPFramework::getSetting('application.url'),
            Resources\MainController::$mountPoint, $resourceName);
    }
?>
