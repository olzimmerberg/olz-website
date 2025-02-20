<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class PlainLogFile implements LogFileInterface {
    use WithUtilsTrait;

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
        $result = filemtime($this->path);
        $this->generalUtils()->checkNotBool($result, 'PlainLogFile::modified failed');
        return $result;
    }

    /** @return resource */
    public function open(string $mode): mixed {
        $result = fopen($this->path, $mode);
        $this->generalUtils()->checkNotBool($result, 'PlainLogFile::open failed');
        return $result;
    }

    /** @param resource $fp */
    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int {
        return fseek($fp, $offset, $whence);
    }

    /** @param resource $fp */
    public function tell(mixed $fp): int {
        $result = ftell($fp);
        $this->generalUtils()->checkNotBool($result, 'PlainLogFile::tell failed');
        return $result;
    }

    /** @param resource $fp */
    public function eof(mixed $fp): bool {
        return feof($fp);
    }

    /** @param resource $fp */
    public function gets(mixed $fp): ?string {
        $result = fgets($fp);
        return $result === false ? null : $result;
    }

    /** @param resource $fp */
    public function close(mixed $fp): bool {
        return fclose($fp);
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'path' => $this->path,
        ]) ?: '{}';
    }

    public static function deserialize(string $serialized): ?LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self($deserialized['path']);
    }
}
