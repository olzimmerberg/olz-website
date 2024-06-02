<?php

namespace Olz\Apps\Logs\Utils;

class GzLogFile implements LogFileInterface {
    public function __construct(
        public string $path,
        public string $filePath,
    ) {
    }

    public function getPath(): string {
        return $this->path;
    }

    public function exists(): bool {
        return is_file($this->filePath);
    }

    public function modified(): int {
        return filemtime($this->filePath);
    }

    public function open(string $mode): mixed {
        $compatibility_map = [
            'r' => 'rb',
            'w' => 'wb',
            'rb' => 'rb',
            'wb' => 'wb',
        ];
        return gzopen($this->filePath, $compatibility_map[$mode]);
    }

    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int {
        if ($whence === SEEK_END) {
            ob_start();
            gzpassthru($fp);
            ob_end_clean();
            $size = gztell($fp);
            return gzseek($fp, $size - $offset, SEEK_SET);
        }
        return gzseek($fp, $offset, $whence);
    }

    public function tell(mixed $fp): int {
        return gztell($fp);
    }

    public function eof(mixed $fp): bool {
        return gzeof($fp);
    }

    public function gets(mixed $fp): bool|string {
        return gzgets($fp);
    }

    public function close(mixed $fp): bool {
        return gzclose($fp);
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'path' => $this->path,
            'filePath' => $this->filePath,
        ]);
    }

    public static function deserialize(string $serialized): ?LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self($deserialized['path'], $deserialized['filePath']);
    }
}
