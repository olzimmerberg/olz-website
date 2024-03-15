<?php

namespace Olz\Entity\Common;

interface DataStorageInterface {
    public static function getEntityNameForStorage(): string;

    public function getEntityIdForStorage(): string;
}
