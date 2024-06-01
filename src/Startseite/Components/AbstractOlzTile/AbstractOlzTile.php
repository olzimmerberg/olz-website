<?php

// =============================================================================
// Abstrakte Klasse einer Startseiten-Kachel.
// =============================================================================

namespace Olz\Startseite\Components\AbstractOlzTile;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\User;

abstract class AbstractOlzTile extends OlzComponent {
    abstract public function getRelevance(?User $user): float;

    /** @param array<string, mixed> $args */
    abstract public function getHtml(array $args = []): string;
}
