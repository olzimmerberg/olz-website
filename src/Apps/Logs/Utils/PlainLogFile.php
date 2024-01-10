<?php

namespace Olz\Apps\Logs\Utils;

class PlainLogFile implements LogFileInterface {
    public function __construct(
        public string $path,
    ) {
    }

    public function getPath() {
        return $this->path;
    }

    public function exists() {
        return is_file($this->path);
    }

    public function modified(): int {
        return filemtime($this->path);
    }

    public function open($mode) {
        return fopen($this->path, $mode);
    }

    public function seek($fp, $offset, $whence = SEEK_SET) {
        return fseek($fp, $offset, $whence);
    }

    public function tell($fp): int {
        return ftell($fp);
    }

    public function eof($fp): bool {
        return feof($fp);
    }

    public function gets($fp) {
        return fgets($fp);
    }

    public function close($fp) {
        return fclose($fp);
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'path' => $this->path,
        ]);
    }

    public static function deserialize(string $serialized): null|LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self($deserialized['path']);
    }
}
