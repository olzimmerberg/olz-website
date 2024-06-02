<?php

namespace Olz\Apps\Logs\Utils;

class PlainLogFile implements LogFileInterface {
    public function __construct(
        protected string $path,
    ) {
    }

    public function getPath(): string {
        return $this->path;
    }

    public function exists(): bool {
        return is_file($this->path);
    }

    public function modified(): int {
        return filemtime($this->path);
    }

    /** @return bool|resource */
    public function open(string $mode): mixed {
        return fopen($this->path, $mode);
    }

    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int {
        return fseek($fp, $offset, $whence);
    }

    public function tell(mixed $fp): int {
        return ftell($fp);
    }

    public function eof(mixed $fp): bool {
        return feof($fp);
    }

    public function gets(mixed $fp): bool|string {
        return fgets($fp);
    }

    public function close(mixed $fp): bool {
        return fclose($fp);
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'path' => $this->path,
        ]);
    }

    public static function deserialize(string $serialized): ?LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self($deserialized['path']);
    }
}
