<?php

namespace Olz\Components\OlzSitemap;

use Olz\Entity\News\NewsEntry;
use Olz\Entity\Role;
use Olz\Entity\Termine\Termin;
use Olz\News\Utils\NewsFilterUtils;
use Olz\Termine\Utils\TermineFilterUtils;
use Olz\Utils\DbUtils;

class OlzSitemap {
    public static function render() {
        $entityManager = DbUtils::fromEnv()->getEntityManager();

        $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $out .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $base_url = 'https://olzimmerberg.ch/';

        $out .= self::getEntry("{$base_url}fuer_einsteiger.php", 'daily', '1.0');
        $out .= self::getEntry("{$base_url}fragen_und_antworten.php", 'daily', '0.8');
        $out .= self::getEntry("{$base_url}startseite.php", 'daily', '0.6');
        $out .= self::getEntry("{$base_url}aktuell.php", 'daily', '0.6');
        $out .= self::getEntry("{$base_url}blog.php", 'daily', '0.4');
        $out .= self::getEntry("{$base_url}termine.php", 'daily', '0.6');
        $out .= self::getEntry("{$base_url}karten.php", 'monthly', '0.5');
        $out .= self::getEntry("{$base_url}material.php", 'monthly', '0.5');
        $out .= self::getEntry("{$base_url}service.php", 'monthly', '0.3');
        $out .= self::getEntry("{$base_url}verein.php", 'monthly', '0.5');
        $out .= self::getEntry("{$base_url}datenschutz.php", 'monthly', '0.1');
        $out .= self::getEntry("{$base_url}trophy.php", 'monthly', '0.5');

        $aktuell_ids = $entityManager->getRepository(NewsEntry::class)->getAllActiveIds();
        foreach ($aktuell_ids as $aktuell_id) {
            $out .= self::getEntry("{$base_url}aktuell.php?id={$aktuell_id}", 'monthly', '0.3');
        }

        $termine_ids = $entityManager->getRepository(Termin::class)->getAllActiveIds();
        foreach ($termine_ids as $termine_id) {
            $out .= self::getEntry("{$base_url}termine.php?id={$termine_id}", 'monthly', '0.2');
        }

        $verein_ressorts = $entityManager->getRepository(Role::class)->getAllActiveRessorts();
        foreach ($verein_ressorts as $verein_ressort) {
            $out .= self::getEntry("{$base_url}verein.php?ressort={$verein_ressort}", 'monthly', '0.5');
        }

        $news_utils = NewsFilterUtils::fromEnv();
        $news_filters = $news_utils->getAllValidFiltersForSitemap();
        foreach ($news_filters as $news_filter) {
            $enc_json_filter = urlencode(json_encode($news_filter));
            $out .= self::getEntry("{$base_url}aktuell.php?filter={$enc_json_filter}", 'monthly', '0.2');
        }

        $termine_utils = TermineFilterUtils::fromEnv();
        $termine_filters = $termine_utils->getAllValidFiltersForSitemap();
        foreach ($termine_filters as $termine_filter) {
            $enc_json_filter = urlencode(json_encode($termine_filter));
            $out .= self::getEntry("{$base_url}termine.php?filter={$enc_json_filter}", 'monthly', '0.2');
        }

        $out .= "</urlset>\n";
        return $out;
    }

    private static function getEntry($url, $change_frequency = null, $priority = null) {
        $change_frequency_line = $change_frequency ? "<changefreq>{$change_frequency}</changefreq>" : '';
        $priority_line = $priority ? "<priority>{$priority}</priority>" : '';
        return <<<ZZZZZZZZZZ
            <url>
                <loc>{$url}</loc>
                {$change_frequency_line}
                {$priority_line}
            </url>\n
        ZZZZZZZZZZ;
    }
}
