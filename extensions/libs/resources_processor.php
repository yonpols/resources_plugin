<?php
    namespace Resources;

    class ResourcesProcessor {
        private $insertedFiles;
        private $contentType = null;
        private $content;
        private $fileName;
        private $isFile = false;

        public function __construct($resourcePath) {
            $pos = strrpos($resourcePath, '.');
            $ext = substr($resourcePath, $pos+1);

            if ($ext == 'js') {
                $prefixPath = 'resources/javascripts';
                $this->contentType = 'text/javascript';
            }
            elseif ($ext == 'css') {
                $prefixPath = 'resources/stylesheets';
                $this->contentType = 'text/css';
            }
            else
                $prefixPath = 'resources/images';

            $filePath = \YPFramework::getComponentPath($resourcePath, $prefixPath);

            if (is_file($filePath)) {
                $this->fileName = $filePath;

                if ($this->contentType) {
                    $filePath = realpath($filePath);
                    $this->insertedFiles = array($filePath => true);

                    $content = $this->processTextualResource($filePath, $prefixPath, ($ext == 'js'? ';': ''));
                    \YPFContentFilter::processContent($filePath, $content);

                    $this->content = $content;
                } else {
                    $this->contentType = \Mime::getMimeFromFile($filePath);
                    $this->isFile = true;
                    $this->fileName = $this->processBinaryResource($this->fileName);
                }
            }
        }

        public function getContentType() {
            return $this->contentType;
        }

        public function getContent() {
            return $this->content;
        }

        public function getFileName() {
            return $this->fileName;
        }

        public function isFile() {
            return $this->isFile;
        }

        private function processTextualResource($fileName, $prefixPath, $joiner = '') {
            if (MainController::$use_framework_cache)
                $content = \YPFCache::fileBased($fileName, null, false);
            else
                $content = false;

            if (!$content) {
                $content = file_get_contents($fileName);

                if (preg_match('/^\\/\\*((?m:.*?))\\*\\//xs', $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $inserted_content = '';
                    $lines = explode("\n", $matches[1][0]);

                    foreach ($lines as $line) {
                        if (preg_match('/^\\s*\\*=\\s*([a-z_]+)\\s*(.+)\\s*$/', $line, $match)) {
                            $command = $match[1];

                            switch($command) {
                                case 'require':
                                    $path = \YPFramework::getComponentPath($match[2], $prefixPath);

                                    if (!$path)
                                        throw new \ErrorComponentNotFound('resource', $match[2]);

                                    $path = realpath($path);
                                    if (!isset ($this->insertedFiles[$path])) {
                                        \Logger::framework('DEBUG:RESOURCES', sprintf('%s %s', $command, $path));
                                        $this->insertedFiles[$path] = true;
                                        $inserted_content .= $joiner.$this->processTextualResource($path, $prefixPath);
                                    }
                                    break;

                                case 'require_tree':
                                case 'require_all':
                                    $paths = \YPFramework::getComponentPath(\YPFramework::getFileName($match[2], '*'), $prefixPath, true, ($command == 'require_all'));

                                    if ($paths === false)
                                        throw new \ErrorComponentNotFound('resource', $match[2]);

                                    foreach ($paths as $path) {
                                        $path = realpath($path);
                                        if (!is_file($path)) continue;

                                        if (!isset ($this->insertedFiles[$path])) {
                                            \Logger::framework('DEBUG:RESOURCES', sprintf('%s %s', $command, $path));
                                            $this->insertedFiles[$path] = true;
                                            $inserted_content .= $joiner.$this->processTextualResource($path, $prefixPath);
                                        }
                                    }
                                    break;
                            }
                        }
                    }

                    $pre_content = substr($content, 0, $matches[0][1]);
                    $post_content = substr($content, $matches[0][1]+strlen($matches[0][0]));
                    $content = $pre_content.$inserted_content.$joiner.$post_content;
                }

                \YPFContentFilter::processContent($fileName, $content);

                if (MainController::$use_framework_cache)
                    \YPFCache::fileBased($fileName, $content, false);
            }
            return $content;
        }

        private function processBinaryResource($fileName) {
            $extension = strtolower(substr($fileName, strrpos($fileName, '.')));

            if (array_search($extension, array('.png', '.jpg', '.jpeg')) === false)
                return $fileName;

            if (MainController::$use_framework_cache)
                $newFileName = \YPFCache::entireFile ($fileName);
            else
                $newFileName = false;

            if (!$newFileName) {
                if (class_exists('Imagick', false)) {
                    $image = new \Imagick($fileName);
                    $image->stripImage();

                    $image->setCompressionQuality(75);

                    $tmp_file = tempnam(\YPFramework::getPaths()->tmp, 'resource');
                    file_put_contents($tmp_file, $image->getImageBlob());

                    $newFileName = \YPFCache::entireFile($fileName, $tmp_file);

                    @unlink($tmp_file);
                } else
                    $newFileName = $fileName;
            }

            return $newFileName;
        }
    }

?>
