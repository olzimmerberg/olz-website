<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit dem Bild der Woche an.
// =============================================================================

namespace Olz\Startseite\Components\OlzWeeklyPictureTile;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\ImageUtils;

class OlzWeeklyPictureTile extends AbstractOlzTile {
    public static function getRelevance(?User $user): float {
        return 0.9;
    }

    public static function render(): string {
        $out = "";
        $out .= "<h2>Bild der Woche</h2>";
        $out .= "<div class='center'>";

        $auth_utils = AuthUtils::fromEnv();
        $entity_manager = DbUtils::fromEnv()->getEntityManager();

        $weekly_picture_repo = $entity_manager->getRepository(WeeklyPicture::class);
        $latest_weekly_picture = $weekly_picture_repo->getLatest();

        $has_access = $auth_utils->hasPermission('weekly_picture');

        if ($latest_weekly_picture) {
            $text = $latest_weekly_picture->getText();
            $id = $latest_weekly_picture->getId();
            $image_id = $latest_weekly_picture->getImageId();
            $alternative_image_id = $latest_weekly_picture->getAlternativeImageId();

            $db_table = 'weekly_picture';
            $image_utils = ImageUtils::fromEnv();
            if (!$alternative_image_id) {
                $out .= $image_utils->olzImage($db_table, $id, $image_id, 256, 'image');
            } else {
                $out .= "<div class='lightgallery'><span onmouseover='olz.olzWeeklyPictureTileSwap()' id='olz-weekly-image'>".$image_utils->olzImage($db_table, $id, $image_id, 256, 'gallery[weekly_picture]')."</span><span onmouseout='olz.olzWeeklyPictureTileUnswap()' id='olz-weekly-alternative-image'>".$image_utils->olzImage($db_table, $id, $alternative_image_id, 256, 'gallery[weekly_picture]')."</span></div>";
            }
            $out .= "<p class='weekly-picture-tile-text'>".$text."</p>";
        }

        if ($has_access) {
            $out .= <<<'ZZZZZZZZZZ'
            <div class='weekly-picture-tile-buttons'>
                <button
                    id='create-weekly-picture-button'
                    class='btn btn-primary'
                    onclick='return olz.initOlzEditWeeklyPictureModal()'
                >
                    <img src='icns/new_white_16.svg' class='noborder' />
                    Neues Bild der Woche
                </button>
            </div>
            ZZZZZZZZZZ;
        }

        $out .= "</div>";
        return $out;
    }
}
