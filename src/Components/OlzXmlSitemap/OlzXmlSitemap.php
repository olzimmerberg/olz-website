<?php

namespace Olz\Components\OlzXmlSitemap;

use Olz\Components\OlzSitemap\OlzSitemap;

class OlzXmlSitemap extends OlzSitemap {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $out = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $out .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

        $entries = $this->getEntries();
        foreach ($entries as $entry) {
            $out .= self::getEntry($entry);
        }

        $out .= "</urlset>\n";
        return $out;
    }

    /** @param array{title: string, description: string, url: string, updates: string, importance: float, level: int} $entry */
    private static function getEntry(array $entry): string {
        $url = htmlentities($entry['url']);
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
