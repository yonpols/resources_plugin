<?php
    class HtmlContentFilter extends YPFContentFilter {
        protected function process($content) {
            require_once 'minify/Minify/HTML.php';
            require_once 'minify/Minify/CSS.php';
            require_once 'minify/JSMin.php';

            return Minify_HTML::minify($content, array(
                'cssMinifier' => array('Minify_CSS', 'minify'),
                'jsMinifier' => array('JSMin', 'minify')
            ));
        }
    }
?>
