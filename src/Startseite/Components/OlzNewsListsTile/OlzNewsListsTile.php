<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevaten News-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsListsTile;

use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\EnvUtils;

class OlzNewsListsTile extends AbstractOlzTile {
    public static function getRelevance(?User $user): float {
        return 0.8;
    }

    public static function render(): string {
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();

        $out = "<h2>News</h2>";
        $out .= "<ul class='links'>";
        $out .= "<li><a href='{$code_href}aktuell.php' class='linkint'>Aktuell</a></li>";
        $out .= "<li><a href='{$code_href}blog.php' class='linkint'>Kaderblog</a></li>";
        $out .= "<li><a href='{$code_href}forum.php' class='linkint'>Forum</a></li>";
        $out .= "<li><a href='{$code_href}galerie.php' class='linkint'>Galerie</a></li>";
        $out .= "</ul>";
        return $out;
    }
}
