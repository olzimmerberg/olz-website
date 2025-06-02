<?php

namespace Olz\Entity\Common;

/**
 * Interface for doctrine entities that are positionable compared to other entities in the same
 * table using the `searchEntities` and `getEntitiesAroundPosition` RPCs. Rgister new entities that
 * implement this interface in `SearchEntitiesEndpoint.php`.
 */
interface PositionableInterface extends SearchableInterface {
    public static function getPositionFieldName(string $entity_field): string;

    public function getPositionForEntityField(string $entity_field): ?float;
}
