<?php

require_once __DIR__.'/config/doctrine_db.php';
require_once __DIR__.'/model/index.php';
require_once __DIR__.'/news/utils/NewsFilterUtils.php';
require_once __DIR__.'/termine/utils/TermineFilterUtils.php';

header('Content-Type: application/xml');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

function get_entry($url, $change_frequency = null, $priority = null) {
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

$base_url = 'https://olzimmerberg.ch/';

echo get_entry("{$base_url}fuer_einsteiger.php", 'daily', '1.0');
echo get_entry("{$base_url}fragen_und_antworten.php", 'daily', '0.8');
echo get_entry("{$base_url}startseite.php", 'daily', '0.6');
echo get_entry("{$base_url}aktuell.php", 'daily', '0.6');
echo get_entry("{$base_url}blog.php", 'daily', '0.4');
echo get_entry("{$base_url}termine.php", 'daily', '0.6');
echo get_entry("{$base_url}karten.php", 'monthly', '0.5');
echo get_entry("{$base_url}material.php", 'monthly', '0.5');
echo get_entry("{$base_url}service.php", 'monthly', '0.3');
echo get_entry("{$base_url}verein.php", 'monthly', '0.5');
echo get_entry("{$base_url}datenschutz.php", 'monthly', '0.1');
echo get_entry("{$base_url}trophy.php", 'monthly', '0.5');

$aktuell_ids = $entityManager->getRepository(NewsEntry::class)->getAllActiveIds();
foreach ($aktuell_ids as $aktuell_id) {
    echo get_entry("{$base_url}aktuell.php?id={$aktuell_id}", 'monthly', '0.3');
}

$termine_ids = $entityManager->getRepository(Termin::class)->getAllActiveIds();
foreach ($termine_ids as $termine_id) {
    echo get_entry("{$base_url}termine.php?id={$termine_id}", 'monthly', '0.2');
}

$verein_ressorts = $entityManager->getRepository(Role::class)->getAllActiveRessorts();
foreach ($verein_ressorts as $verein_ressort) {
    echo get_entry("{$base_url}verein.php?ressort={$verein_ressort}", 'monthly', '0.5');
}

$news_utils = NewsFilterUtils::fromEnv();
$news_filters = $news_utils->getAllValidFiltersForSitemap();
foreach ($news_filters as $news_filter) {
    $enc_json_filter = urlencode(json_encode($news_filter));
    echo get_entry("{$base_url}aktuell.php?filter={$enc_json_filter}", 'monthly', '0.2');
}

$termine_utils = TermineFilterUtils::fromEnv();
$termine_filters = $termine_utils->getAllValidFiltersForSitemap();
foreach ($termine_filters as $termine_filter) {
    $enc_json_filter = urlencode(json_encode($termine_filter));
    echo get_entry("{$base_url}termine.php?filter={$enc_json_filter}", 'monthly', '0.2');
}

echo "</urlset>\n";
