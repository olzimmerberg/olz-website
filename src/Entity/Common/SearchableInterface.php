<?php

namespace Olz\Entity\Common;

interface SearchableInterface {
    public static function getIdFieldNameForSearch(): string;

    public function getIdForSearch(): int;

    public static function getFieldNamesForSearch(): array;

    public function getTitleForSearch(): string;
}
