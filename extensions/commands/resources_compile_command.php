<?php
    class ResourcesCompileCommand extends YPFCommand {
        public function getDescription() {
            return 'compiles a resource to avoid processing of files at the web server';
        }

        public function help($parameters) {
            echo "ypf resources.compile resource-name [compiled-file]\n".
                 "Compiles a resource to avoid processing of files at the web server\n".
                 "   resource-name:    name of the resource as it would be called in resource_tag\n".
                 "   compiled-file:    (optional) path of the file compiled. If none specified\n".
                 "                     it will be placed under {WWW_PATH}/resources/\n";

            return YPFCommand::RESULT_OK;
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

            include 'resources/processor.php';
            Resources\MainController::$minify_stylesheets = true;
            Resources\MainController::$minify_javascripts = true;

            $resource = $parameters[0];

            $processor = new Resources\Processor($resource);

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

                if ($processor->isFile())
                    $result = copy ($processor->getFileName (), $destination);
                else
                    $result = file_put_contents ($destination, $processor->getContent ());
            } else
                $this->exitNow(YPFCommand::RESULT_FILES_ERROR, 'resource not found.');
        }
    }
?>
