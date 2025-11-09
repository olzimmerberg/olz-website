<?php

namespace Olz\Components\OlzHtmlSitemap;

use Olz\Components\OlzSitemap\OlzSitemap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzHtmlSitemapParams extends HttpParams {
}

class OlzHtmlSitemap extends OlzSitemap {
    public static string $title = "Sitemap";
    public static string $description = "Eine Übersicht über alle aktiven Inhalte der Website der OL Zimmerberg.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzHtmlSitemapParams::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);
        $out .= "<div class='content-full olz-html-sitemap'>";
        $out .= "<h1>Sitemap</h1>";

        $entries = $this->getEntries();
        foreach ($entries as $entry) {
            $out .= self::getEntry($entry);
        }

        $out .= "</div>";
        $out .= OlzFooter::render();
        return $out;
    }

    /** @param array{title: string, description: string, url: string, updates: string, importance: float, level: int} $entry */
    private static function getEntry(array $entry): string {
        $url = $entry['url'];
        $title = $entry['title'];
        $description = $entry['description'];
        $level = $entry['level'];
        return <<<ZZZZZZZZZZ
            <div class="entry level-{$level}">
                <a href="{$url}">
                    <span class="title">{$title}</span><br />
                    <span class="description">{$description}</span>
                </a>
            </div>
            ZZZZZZZZZZ;
    }
}
