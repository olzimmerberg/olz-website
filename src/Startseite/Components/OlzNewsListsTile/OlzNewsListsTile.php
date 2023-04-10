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

        $out = "<h2>News</h2>";
        $out .= "<ul class='links'>";
        $aktuell_url = $this->getNewsUrl('aktuell');
        $out .= "<li><a href='{$aktuell_url}' class='linkint'>Aktuell</a></li>";
        $kaderblog_url = $this->getNewsUrl('kaderblog');
        $out .= "<li><a href='{$kaderblog_url}' class='linkint'>Kaderblog</a></li>";
        $forum_url = $this->getNewsUrl('forum');
        $out .= "<li><a href='{$forum_url}' class='linkint'>Forum</a></li>";
        $galerie_url = $this->getNewsUrl('galerie');
        $out .= "<li><a href='{$galerie_url}' class='linkint'>Galerie</a></li>";
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
