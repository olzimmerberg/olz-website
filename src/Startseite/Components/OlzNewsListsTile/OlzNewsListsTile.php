<?php

// =============================================================================
// Zeigt eine Startseiten-Kachel mit relevaten News-Links an.
// =============================================================================

namespace Olz\Startseite\Components\OlzNewsListsTile;

use Olz\Entity\User;
use Olz\News\Utils\NewsFilterUtils;
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
        $aktuell_url = self::getNewsUrl('aktuell');
        $out .= "<li><a href='{$aktuell_url}' class='linkint'>Aktuell</a></li>";
        $kaderblog_url = self::getNewsUrl('kaderblog');
        $out .= "<li><a href='{$kaderblog_url}' class='linkint'>Kaderblog</a></li>";
        $forum_url = self::getNewsUrl('forum');
        $out .= "<li><a href='{$forum_url}' class='linkint'>Forum</a></li>";
        $galerie_url = self::getNewsUrl('galerie');
        $out .= "<li><a href='{$galerie_url}' class='linkint'>Galerie</a></li>";
        $out .= "</ul>";
        return $out;
    }

    private static function getNewsUrl($format = null) {
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();

        $news_filter_utils = NewsFilterUtils::fromEnv();
        $filter = $news_filter_utils->getDefaultFilter();
        if ($format) {
            $filter['format'] = $format;
        }
        $enc_json_filter = urlencode(json_encode($filter));
        return "{$code_href}aktuell.php?filter={$enc_json_filter}";
    }
}
