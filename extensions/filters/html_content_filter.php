<?php
    class HtmlContentFilter extends YPFContentFilter {
        protected function process($content) {
            require_once 'minify_css/HTML.php';
            require_once 'minify_css/CSS.php';
            require_once 'minify_css/JSMin.php';

            return Minify_HTML::minify($content, array(
                'cssMinifier' => array('Minify_CSS', 'minify'),
                'jsMinifier' => array('JSMin', 'minify')
            ));
        }
    }
?>
