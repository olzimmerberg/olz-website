<?php

namespace Olz\Entity\Common;

use Doctrine\Common\Collections\Expr\Expression;

/**
 * Interface for doctrine entities that are searchable using the `searchEntities` RPC. Rgister new
 * entities that implement this interface in `SearchEntitiesEndpoint.php`.
 */
interface SearchableInterface {
    public static function getIdFieldNameForSearch(): string;

    public function getIdForSearch(): int;

    public function getTitleForSearch(): string;

    public static function getCriteriaForFilter(string $key, string $value): Expression;

    public static function getCriteriaForQuery(string $query): Expression;
}
