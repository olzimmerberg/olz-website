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

        $entity_manager = $this->dbUtils()->getEntityManager();
        $image_utils = ImageUtils::fromEnv();

        $weekly_picture_repo = $entity_manager->getRepository(WeeklyPicture::class);
        $latest_weekly_pictures = $weekly_picture_repo->getLatestThree();
        $index = 0;
        $carousel_inner = '';
        foreach ($latest_weekly_pictures as $weekly_picture) {
            $text = $weekly_picture->getText();
            $id = $weekly_picture->getId();
            $image_id = $weekly_picture->getImageId();
            $alternative_image_id = $weekly_picture->getAlternativeImageId();

            $active_class = $index === 0 ? ' active' : '';
            $carousel_inner .= "<div class='carousel-item{$active_class}'>";
            if (!$alternative_image_id) {
                $carousel_inner .= $image_utils->olzImage('weekly_picture', $id, $image_id, 512, 'image');
            } else {
                $carousel_inner .= "<div class='lightgallery'><span onmouseover='olz.olzWeeklyPictureTileSwap()' id='olz-weekly-image'>".$image_utils->olzImage('weekly_picture', $id, $image_id, 512, 'gallery[weekly_picture]')."</span><span onmouseout='olz.olzWeeklyPictureTileUnswap()' id='olz-weekly-alternative-image'>".$image_utils->olzImage('weekly_picture', $id, $alternative_image_id, 512, 'gallery[weekly_picture]')."</span></div>";
            }
            $carousel_inner .= "<div class='weekly-picture-tile-text'>".$text."</div>";
            $carousel_inner .= "</div>";
            $index++;
        }

        $out .= <<<ZZZZZZZZZZ
        <h2 class='weekly-picture-h2'>Bild der Woche {$mgmt_html}</h2>
        <div
            id='weekly-picture-carousel'
            class='carousel slide'
            data-bs-ride='carousel'
            data-bs-interval='15000'
            data-bs-wrap="false"
        >
            <div class='carousel-inner'>{$carousel_inner}</div>
            <button
                class="carousel-control-prev"
                data-bs-target="#weekly-picture-carousel"
                data-bs-slide="prev"
            >
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button
                class="carousel-control-next"
                data-bs-target="#weekly-picture-carousel"
                data-bs-slide="next"
            >
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
        ZZZZZZZZZZ;

        return $out;
    }
}
