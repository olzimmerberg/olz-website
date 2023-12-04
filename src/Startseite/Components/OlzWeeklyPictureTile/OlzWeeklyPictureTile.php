<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit dem Bild der Woche an.
// =============================================================================

namespace Olz\Startseite\Components\OlzWeeklyPictureTile;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Entity\User;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;
use Olz\Utils\ImageUtils;

class OlzWeeklyPictureTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.9;
    }

    public function getHtml($args = []): string {
        $out = "";

        $has_access = $this->authUtils()->hasPermission('weekly_picture');
        $code_href = $this->envUtils()->getCodeHref();

        $mgmt_html = '';
        if ($has_access) {
            $mgmt_html .= <<<ZZZZZZZZZZ
            <a
                href='#'
                id='create-weekly-picture-button'
                class='header-link'
                onclick='return olz.initOlzEditWeeklyPictureModal()'
            >
                <img
                    src='{$code_href}assets/icns/new_white_16.svg'
                    alt='+'
                    class='header-link-icon'
                    title='Neues Bild der Woche'
                />
            </a>
            ZZZZZZZZZZ;
        }

        $out .= "<h2 class='weekly-picture-h2'>Bild der Woche {$mgmt_html}</h2>";
        $out .= "<div class='weekly-picture-tile-container center'>";

        $entity_manager = $this->dbUtils()->getEntityManager();

        $weekly_picture_repo = $entity_manager->getRepository(WeeklyPicture::class);
        $latest_weekly_picture = $weekly_picture_repo->getLatest();

        if ($latest_weekly_picture) {
            $text = $latest_weekly_picture->getText();
            $id = $latest_weekly_picture->getId();
            $image_id = $latest_weekly_picture->getImageId();
            $alternative_image_id = $latest_weekly_picture->getAlternativeImageId();

            $image_utils = ImageUtils::fromEnv();
            if (!$alternative_image_id) {
                $out .= $image_utils->olzImage('weekly_picture', $id, $image_id, 512, 'image');
            } else {
                $out .= "<div class='lightgallery'><span onmouseover='olz.olzWeeklyPictureTileSwap()' id='olz-weekly-image'>".$image_utils->olzImage('weekly_picture', $id, $image_id, 512, 'gallery[weekly_picture]')."</span><span onmouseout='olz.olzWeeklyPictureTileUnswap()' id='olz-weekly-alternative-image'>".$image_utils->olzImage('weekly_picture', $id, $alternative_image_id, 512, 'gallery[weekly_picture]')."</span></div>";
            }
            $out .= "<div class='weekly-picture-tile-text'>".$text."</div>";
        }

        $out .= "</div>";
        return $out;
    }
}
