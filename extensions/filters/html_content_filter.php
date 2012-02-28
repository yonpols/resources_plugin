<?php
    class HtmlContentFilter extends YPFContentFilter {
        protected function process($content) {
            require_once 'Minify/HTML.php';
            return Minify_HTML::minify($content);
        }
    }
?>
