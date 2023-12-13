<?php

namespace Olz\Components\OlzXmlSitemap;

use Olz\Components\OlzSitemap\OlzSitemap;

class OlzXmlSitemap extends OlzSitemap {
    public function getHtml($args = []): string {
        $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $out .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $entries = $this->getEntries();
        foreach ($entries as $entry) {
            $out .= self::getEntry($entry);
        }

        $out .= "</urlset>\n";
        return $out;
    }

    private static function getEntry($entry) {
        $url = $entry['url'];
        $change_frequency = $entry['updates'];
        $priority = $entry['importance'];
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
