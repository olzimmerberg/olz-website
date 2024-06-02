<?php

namespace Olz\Apps\Logs\Utils;

interface LogFileInterface {
    public function getPath(): string;

    public function exists(): bool;

    public function modified(): int;

    /** @return bool|resource */
    public function open(string $mode): mixed;

    /** @param resource $fp */
    public function seek(mixed $fp, int $offset, int $whence = SEEK_SET): int;

    /** @param resource $fp */
    public function tell(mixed $fp): int;

    /** @param resource $fp */
    public function eof(mixed $fp): bool;

    /** @param resource $fp */
    public function gets(mixed $fp): bool|string;

    /** @param resource $fp */
    public function close(mixed $fp): bool;

    public function serialize(): string;

    public static function deserialize(string $serialized): ?LogFileInterface;
}
