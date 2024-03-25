<?php

namespace Olz\Apps\Logs\Utils;

interface LogFileInterface {
    public function getPath();

    public function exists();

    public function modified(): int;

    public function open($mode);

    public function seek($fp, $offset, $whence = SEEK_SET);

    public function tell($fp): int;

    public function eof($fp): bool;

    public function gets($fp);

    public function close($fp);

    public function serialize(): string;

    public static function deserialize(string $serialized): ?LogFileInterface;
}
