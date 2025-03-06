<?php

namespace Olz\Apps\Logs\Utils;

use Olz\Utils\WithUtilsTrait;

class GzLogFile implements LogFileInterface {
    use WithUtilsTrait;

    public function __construct(
        protected string $path,
        protected string $indexPath,
    ) {
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getIndexPath(): string {
        return $this->indexPath;
    }

    public function exists(): bool {
        return is_file($this->path);
    }

    public function modified(): int {
        $result = filemtime($this->path);
        $this->generalUtils()->checkNotBool($result, 'GzLogFile::modified failed');
        return $result;
    }

    /** @return resource */
    public function open(string $mode): mixed {
        $compatibility_map = [
            'r' => 'rb',
            'w' => 'wb',
            'rb' => 'rb',
            'wb' => 'wb',
        ];
        $result = gzopen($this->path, $compatibility_map[$mode]);
        $this->generalUtils()->checkNotBool($result, 'GzLogFile::open failed');
        return $result;
    }

    /** @param resource $fp */
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

    /** @param resource $fp */
    public function tell(mixed $fp): int {
        $result = gztell($fp);
        $this->generalUtils()->checkNotBool($result, 'GzLogFile::tell failed');
        return $result;
    }

    /** @param resource $fp */
    public function eof(mixed $fp): bool {
        return gzeof($fp);
    }

    /** @param resource $fp */
    public function gets(mixed $fp): ?string {
        $result = gzgets($fp);
        return $result === false ? null : $result;
    }

    /** @param resource $fp */
    public function close(mixed $fp): bool {
        return gzclose($fp);
    }

    public function serialize(): string {
        return json_encode([
            'class' => self::class,
            'path' => $this->path,
            'indexPath' => $this->indexPath,
        ]) ?: '{}';
    }

    public static function deserialize(string $serialized): ?LogFileInterface {
        $deserialized = json_decode($serialized, true);
        if ($deserialized['class'] !== self::class) {
            return null;
        }
        return new self($deserialized['path'], $deserialized['indexPath']);
    }
}
