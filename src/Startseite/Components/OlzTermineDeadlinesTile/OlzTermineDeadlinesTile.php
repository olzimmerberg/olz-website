<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit den nächsten Meldeschlüssen an.
// =============================================================================

namespace Olz\Startseite\Components\OlzTermineDeadlinesTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzTermineDeadlinesTile extends AbstractOlzTile {
    public static function getRelevance(?User $user): float {
        return 0.7;
    }

    public static function render(): string {
        $out = "<h2>Meldeschlüsse</h2>";
        $out .= "<ul class='links'>";
        $out .= "<li>TODO</li>";
        $out .= "</ul>";
        return $out;
    }
}
