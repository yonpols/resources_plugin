<?php
    class CoffeContentFilter extends YPFContentFilter {
        protected function process($content) {
            $descriptorspec = array(
               0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
               1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
               2 => array("pipe", "w"),
            );

            $tmp_input = tempnam(YPFramework::getPaths()->tmp, 'less_input');
            if (file_put_contents($tmp_input, $content)) {
                $process = proc_open(sprintf('coffee -p -c "%s"', escapeshellarg($arg)), $descriptorspec, $pipes);

                if (is_resource($process)) {
                    // $pipes now looks like this:
                    // 0 => writeable handle connected to child stdin
                    // 1 => readable handle connected to child stdout

                    fclose($pipes[0]);

                    $new_content = stream_get_contents($pipes[1]);
                    $errors = stream_get_contents($pipes[2]);
                    fclose($pipes[1]);
                    fclose($pipes[2]);

                    // It is important that you close any pipes before calling
                    // proc_close in order to avoid a deadlock
                    $return_value = proc_close($process);

                    @unlink($tmp_input);

                    if (!$return_value)
                        return $new_content;
                    else
                        Logger::framework ('DEBUG:ERROR', $errors);
                }

            }

            return $content;
        }
    }
?>
