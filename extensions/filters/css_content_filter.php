<?php
    class CssContentFilter extends YPFContentFilter {
        protected function process($content) {
            if (!Resources\MainController::$minify_stylesheets)
                return $content;

            require_once 'minify/Minify/CSS.php';
            return Minify_CSS::minify($content);
        }
    }
?>
