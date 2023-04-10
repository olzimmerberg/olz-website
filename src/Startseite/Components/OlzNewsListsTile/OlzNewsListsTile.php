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
        $data_href = $this->envUtils()->getDataHref();

        $out = "<h2>News</h2>";
        $out .= "<ul class='links'>";
        $aktuell_url = $this->getNewsUrl('aktuell');
        $out .= <<<ZZZZZZZZZZ
        <li><a href='{$aktuell_url}' class='linkint'>
            <img src='{$data_href}icns/entry_type_aktuell_20.svg' alt='' class='link-icon'>
            Aktuell
        </a></li>
        ZZZZZZZZZZ;
        $kaderblog_url = $this->getNewsUrl('kaderblog');
        $out .= <<<ZZZZZZZZZZ
        <li><a href='{$kaderblog_url}' class='linkint'>
            <img src='{$data_href}icns/entry_type_kaderblog_20.svg' alt='' class='link-icon'>
            Kaderblog
        </a></li>
        ZZZZZZZZZZ;
        $forum_url = $this->getNewsUrl('forum');
        $out .= <<<ZZZZZZZZZZ
        <li><a href='{$forum_url}' class='linkint'>
            <img src='{$data_href}icns/entry_type_forum_20.svg' alt='' class='link-icon'>
            Forum
        </a></li>
        ZZZZZZZZZZ;
        $galerie_url = $this->getNewsUrl('galerie');
        $out .= <<<ZZZZZZZZZZ
        <li><a href='{$galerie_url}' class='linkint'>
            <img src='{$data_href}icns/entry_type_gallery_20.svg' alt='' class='link-icon'>
            Galerie
        </a></li>
        ZZZZZZZZZZ;
        $out .= "</ul>";
        return $out;
    }

    private function getNewsUrl($format = null) {
        $code_href = $this->envUtils()->getCodeHref();

        $news_filter_utils = NewsFilterUtils::fromEnv();
        $filter = $news_filter_utils->getDefaultFilter();
        if ($format) {
            $filter['format'] = $format;
        }
        $enc_json_filter = urlencode(json_encode($filter));
        return "{$code_href}aktuell.php?filter={$enc_json_filter}";
    }
}
