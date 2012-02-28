<?php
    namespace Resources;

    class MainController extends \YPFControllerBase {
        public static $mountPoint = null;

        public static $javascriptsToInclude = array();
        public static $stylesheetsToInclude = array();

        //Options
        public static $not_found_error = 'Not Found';
        public static $use_client_cache = true;
        public static $use_framework_cache = true;
        public static $images_cache_time = 2592000;
        public static $javascripts_cache_time = 2592000;
        public static $stylesheets_cache_time = 2592000;
        public static $minify_javascripts = true;
        public static $minify_stylesheets = true;
        public static $minify_html = true;

        public static function initialize() {
            if (\YPFramework::inProduction()) {
                self::$not_found_error = \YPFramework::getSetting('resources.not_found_error', 'Resource not Found');
                self::$use_client_cache = \YPFramework::getSetting('resources.use_client_cache', true);
                self::$use_framework_cache = \YPFramework::getSetting('resources.use_framework_cache', true);
                self::$images_cache_time = \YPFramework::getSetting('resources.images_cache_time', 2592000);
                self::$javascripts_cache_time = \YPFramework::getSetting('resources.javascripts_cache_time', 2592000);
                self::$stylesheets_cache_time = \YPFramework::getSetting('resources.stylesheets_cache_time', 2592000);
                self::$minify_javascripts = \YPFramework::getSetting('resources.minify_javascripts', true);
                self::$minify_stylesheets = \YPFramework::getSetting('resources.minify_stylesheets', true);
                self::$minify_html = \YPFramework::getSetting('resources.minify_html', true);
            } else {
                self::$not_found_error = \YPFramework::getSetting('resources.not_found_error', 'Resource not Found');
                self::$use_client_cache = \YPFramework::getSetting('resources.use_client_cache', false);
                self::$use_framework_cache = \YPFramework::getSetting('resources.use_framework_cache', true);
                self::$images_cache_time = \YPFramework::getSetting('resources.images_cache_time', 0);
                self::$javascripts_cache_time = \YPFramework::getSetting('resources.javascripts_cache_time', 0);
                self::$stylesheets_cache_time = \YPFramework::getSetting('resources.stylesheets_cache_time', 0);
                self::$minify_javascripts = \YPFramework::getSetting('resources.minify_javascripts', false);
                self::$minify_stylesheets = \YPFramework::getSetting('resources.minify_stylesheets', false);
                self::$minify_html = \YPFramework::getSetting('resources.minify_html', false);
            }
        }

        public function get() {
            include 'resources_processor.php';

            $resourcePath = $this->params['path'];
            $processor = new ResourcesProcessor($resourcePath);

            $response = new \YPFResponse($this->application, $this, 'get');

            if (($contentType = $processor->getContentType())) {
                $response->header('Content-Type', $contentType);
                if (!self::$use_client_cache)
                    $response->header ('Expires', http_date (time()-self::$images_cache_time));
                    //$response->header('Cache-Control', 'no-cache');
                else {

                    if ($contentType=='text/css') {
                        $cache_time = self::$stylesheets_cache_time;
                        $response->hader('Vary', 'Accept-Encoding');
                    } elseif ($contentType=='text/javascript') {
                        $cache_time = self::$javascripts_cache_time;
                        $response->hader('Vary', 'Accept-Encoding');
                    } else
                        $cache_time = self::$images_cache_time;

                    $response->header ('Expires', http_date(time()+$cache_time));
                    $response->header('Last-Modified', \http_date(filemtime($processor->getFileName())));
                }
                if ($processor->isFile())
                    $response->sendFile ($processor->getFileName());
                else
                    $response->sendData($processor->getContent());
                exit;
            } else {
                $response->status(404, self::$not_found_error);
                $response->sendData('');
            }
        }
    }
?>
