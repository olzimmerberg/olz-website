<?php

// =============================================================================
// Abstrakte Klasse einer Startseiten-Kachel.
// =============================================================================

namespace Olz\Startseite\Components\AbstractOlzTile;

use Olz\Components\Common\OlzComponent;
use Olz\Entity\User;

abstract class AbstractOlzTile extends OlzComponent {
    abstract public function getRelevance(?User $user): float;

    abstract public function getHtml($args = []): string;
}
