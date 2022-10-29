<?php

// =============================================================================
// Zeigt eine personalisierte Startseite an.
// =============================================================================

namespace Olz\Startseite\Components\OlzCustomizableHome;

use Olz\Startseite\Components\OlzCustomTile\OlzCustomTile;
use Olz\Startseite\Components\OlzForBeginnersTile\OlzForBeginnersTile;
use Olz\Startseite\Components\OlzNewsListsTile\OlzNewsListsTile;
use Olz\Startseite\Components\OlzTermineDeadlinesTile\OlzTermineDeadlinesTile;
use Olz\Startseite\Components\OlzTermineListsTile\OlzTermineListsTile;
use Olz\Startseite\Components\OlzTermineUpdatesTile\OlzTermineUpdatesTile;
use Olz\Startseite\Components\OlzWeeklyPictureTile\OlzWeeklyPictureTile;
use Olz\Utils\AuthUtils;

class OlzCustomizableHome {
    public static function render($args = []) {
        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();

        $tile_classes = [
            OlzForBeginnersTile::class,
            OlzWeeklyPictureTile::class,
            OlzTermineListsTile::class,
            OlzTermineDeadlinesTile::class,
            OlzTermineUpdatesTile::class,
            OlzNewsListsTile::class,
            OlzCustomTile::class,
        ];

        $out = '';

        $out .= "<div class='content-full'>";
        $out .= "<div class='olz-customizable-home'>";

        $tiles = [];
        foreach ($tile_classes as $tile_class) {
            $relevance = $tile_class::getRelevance($user);
            if ($relevance === 0.0) {
                continue;
            }
            $tiles[] = [
                'id' => self::getIdFromClass($tile_class),
                'html' => $tile_class::render(),
                'relevance' => $relevance,
            ];
        }

        usort($tiles, function ($tile_a, $tile_b) {
            return $tile_a['relevance'] < $tile_b['relevance'];
        });

        foreach ($tiles as $tile) {
            $tile_id = $tile['id'];
            $out .= "<div class='tile-container'><div class='tile tile-id-{$tile_id}'>";
            $out .= $tile['html'];
            $out .= "</div></div>";
        }

        $out .= "</div>";
        $out .= "</div>";

        return $out;
    }

    protected static function getIdFromClass($class) {
        $class_name = strval($class);
        $base_class_name = substr($class_name, strrpos($class_name, '\\') + 1);
        return preg_replace_callback('/[A-Z]/', function ($matches) {
            return '-'.strtolower($matches[0]);
        }, lcfirst($base_class_name));
    }
}
