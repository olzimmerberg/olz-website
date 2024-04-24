<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevaten News-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsListsTile;

use Olz\Entity\User;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Startseite\Components\AbstractOlzTile\AbstractOlzTile;

class OlzNewsListsTile extends AbstractOlzTile {
    public function getRelevance(?User $user): float {
        return 0.8;
    }

    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $news_filter_utils = NewsFilterUtils::fromEnv();

        $out = "<h2>News</h2>";
        $out .= "<ul class='links'>";
        $aktuell_url = $news_filter_utils->getUrl(['format' => 'aktuell']);
        $out .= <<<ZZZZZZZZZZ
            <li><a href='{$aktuell_url}'>
                <img src='{$code_href}assets/icns/entry_type_aktuell_20.svg' alt='Aktuell' class='link-icon'>
                <b>Aktuell</b>
            </a></li>
            ZZZZZZZZZZ;
        $kaderblog_url = $news_filter_utils->getUrl(['format' => 'kaderblog']);
        $out .= <<<ZZZZZZZZZZ
            <li><a href='{$kaderblog_url}'>
                <img src='{$code_href}assets/icns/entry_type_kaderblog_20.svg' alt='Kaderblog' class='link-icon'>
                <b>Kaderblog</b>
            </a></li>
            ZZZZZZZZZZ;
        $forum_url = $news_filter_utils->getUrl(['format' => 'forum']);
        $out .= <<<ZZZZZZZZZZ
            <li><a href='{$forum_url}'>
                <img src='{$code_href}assets/icns/entry_type_forum_20.svg' alt='Forum' class='link-icon'>
                <b>Forum</b>
            </a></li>
            ZZZZZZZZZZ;
        $galerie_url = $news_filter_utils->getUrl(['format' => 'galerie']);
        $out .= <<<ZZZZZZZZZZ
            <li><a href='{$galerie_url}'>
                <img src='{$code_href}assets/icns/entry_type_gallery_20.svg' alt='Galerie' class='link-icon'>
                <b>Galerie</b>
            </a></li>
            ZZZZZZZZZZ;
        $out .= "</ul>";
        return $out;
    }
}
