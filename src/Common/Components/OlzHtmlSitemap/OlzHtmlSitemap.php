<?php

namespace Olz\Common\Components\OlzHtmlSitemap;

use Olz\Common\Components\OlzFooter\OlzFooter;
use Olz\Common\Components\OlzHeader\OlzHeader;
use Olz\Common\Components\OlzSitemap\OlzSitemap;
use Olz\Common\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzHtmlSitemapParams extends HttpParams {
}

class OlzHtmlSitemap extends OlzSitemap {
    public function getPageTitle(): string {
        return "Sitemap";
    }

    public function getPageDescription(): string {
        return "Eine Übersicht über alle öffentlichen Inhalte der Website der OL Zimmerberg.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzHtmlSitemapParams::class);

        $out = OlzHeader::render([
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
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
