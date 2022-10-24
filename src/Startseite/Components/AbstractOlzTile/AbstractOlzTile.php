<?php

// =============================================================================
// Abstrakte Klasse einer Startseiten-Kachel.
// =============================================================================

namespace Olz\Startseite\Components\AbstractOlzTile;

use Olz\Entity\User;

abstract class AbstractOlzTile {
    abstract public static function getRelevance(?User $user): float;

    abstract public static function render(): string;
}
