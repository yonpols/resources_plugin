<?php
    class JsContentFilter extends YPFContentFilter {
        protected function process($content) {
            if (!Resources\MainController::$minify_javascripts)
                return $content;

            if (YPFramework::getSetting('resources.use_uglify', false)) {
                if ($this->uglify($content))
                    return $content;
            }

            $this->jsmin($content);
            return $content;
        }

        protected function jsmin(&$content) {
            require_once 'minify_css/JSMin.php';
            $content = JSMin::minify($content);
            return true;
        }

        /*
         * This is to use UglifyJS. It needs node.js installed and UglifyJs installed with
         * npm install -g uglifyjs
         *
         */
        protected function uglify(&$content) {
            $descriptorspec = array(
               0 => array("pipe", "r"),  // stdin
               1 => array("pipe", "w"),  // stdout
               2 => array("pipe", "w"),  // stderr
            );

            $process = proc_open('uglifyjs', $descriptorspec, $pipes);

            if (is_resource($process)) {
                fwrite($pipes[0], $content);
                fclose($pipes[0]);

                $new_content = stream_get_contents($pipes[1]);
                $errors = stream_get_contents($pipes[2]);
                fclose($pipes[1]);
                fclose($pipes[2]);

                $return_value = proc_close($process);

                if (!$return_value) {
                    $content = $new_content;
                    return true;
                } else
                    Logger::framework ('DEBUG:ERROR', $errors);
            }

            return false;
        }
    }
?>
