<?php
    class ResourcesCompileCommand extends YPFCommand {
        public function getDescription() {
            return '';
        }

        public function help($parameters) {
            ;
        }

        public function run($parameters) {
            if (count($parameters) < 1) {
                $this->help ($parameters);
                $this->exitNow(YPFCommand::RESULT_INVALID_PARAMETERS);
            }
            if (count($parameters) > 2) {
                $this->help ($parameters);
                $this->exitNow(YPFCommand::RESULT_INVALID_PARAMETERS);
            }

            include 'resources_processor.php';
            $resource = $parameters[0];

            $processor = new Resources\ResourcesProcessor($resource);

            if ($processor->getContentType()) {
                if (count($parameters) > 1)
                    $destination = $parameters[1];
                else {
                    $destination = YPFramework::getFileName (YPFramework::getPaths()->www, Resources\MainController::$mountPoint);
                    if (!is_dir($destination))
                        mkdir ($destination);
                }

                if (is_dir($destination))
                    $destination = YPFramework::getFileName ($destination, basename($resource));

                if ($processor->getFileName())
                    $result = copy ($processor->getFileName (), $destination);
                else
                    $result = file_put_contents ($destination, $processor->getContent ());
            } else
                $this->exitNow(YPFCommand::RESULT_FILES_ERROR, 'resource not found.');
        }
    }
?>
