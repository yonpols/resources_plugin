<?php
    class LessContentFilter extends YPFContentFilter {
        protected function process($content) {
            $descriptorspec = array(
               0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
               1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
               2 => array("pipe", "w"),
            );

            $tmp_input = tempnam(YPFramework::getPaths()->tmp, 'less_input');
            $tmp_output = tempnam(YPFramework::getPaths()->tmp, 'less_input');

            if (file_put_contents($tmp_input, $content)) {
                system(sprintf('lessc %s %s', escapeshellarg($tmp_input), escapeshellarg($tmp_output)), $return_value);
                $new_content = file_get_contents($tmp_output);

                @unlink($tmp_input);
                @unlink($tmp_output);

                if (!$return_value && $new_content)
                    return $new_content;
            }

            return $content;
        }
    }
?>
