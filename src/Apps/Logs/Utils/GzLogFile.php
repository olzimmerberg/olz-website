<?php

namespace Olz\Apps\Logs\Utils;

class GzLogFile implements LogFileInterface {
    public function __construct(
        public string $path,
        public string $filePath,
    ) {
    }

    public function getPath() {
        return $this->path;
    }

    public function exists() {
        return is_file($this->filePath);
    }

    public function modified(): int {
        return filemtime($this->filePath);
    }

    public function open($mode) {
        $compatibility_map = [
            'r' => 'rb',
            'w' => 'wb',
            'rb' => 'rb',
            'wb' => 'wb',
        ];
        return gzopen($this->filePath, $compatibility_map[$mode]);
    }

    public function seek($fp, $offset, $whence = SEEK_SET) {
        if ($whence === SEEK_END) {
            ob_start();
            gzpassthru($fp);
            ob_end_clean();
            $size = gztell($fp);
            return gzseek($fp, $size - $offset, SEEK_SET);
        }
        return gzseek($fp, $offset, $whence);
    }

    public function tell($fp): int {
        return gztell($fp);
    }

    public function eof($fp): bool {
        return gzeof($fp);
    }

    public function gets($fp) {
        return gzgets($fp);
    }

    public function close($fp) {
        return gzclose($fp);
    }
}
