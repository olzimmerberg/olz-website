<?php

namespace Olz\Service\Components\OlzService;

use Olz\Components\Apps\OlzAppsList\OlzAppsList;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Service\Components\OlzDownloads\OlzDownloads;
use Olz\Service\Components\OlzLinks\OlzLinks;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzServiceParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzService extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        $downloads_where = implode(' AND ', array_map(function ($term) {
            return "name LIKE '%{$term}%'";
        }, $terms));
        $links_where = implode(' AND ', array_map(function ($term) {
            return "(name LIKE '%{$term}%' OR url LIKE '%{$term}%')";
        }, $terms));
        // TODO better icons
        $static_page_query = $this->searchUtils()->getStaticResultQuery([
            'link' => "{$code_href}karten",
            'icon' => "{$code_href}assets/icns/link_map_16.svg",
            'title' => $this->getPageTitle(),
            'text' => $this->getPageDescription(),
        ], $terms);
        return [
            'with' => $static_page_query['with'],
            'query' => <<<ZZZZZZZZZZ
                    SELECT
                        '{$code_href}service' AS link,
                        '{$code_href}assets/icns/link_internal_16.svg' AS icon,
                        NULL AS date,
                        CONCAT('Download: ', name) AS title,
                        NULL AS text,
                        0.9 AS time_relevance
                    FROM downloads
                    WHERE
                        on_off = '1'
                        AND {$downloads_where}
                UNION ALL
                    SELECT
                        '{$code_href}service' AS link,
                        '{$code_href}assets/icns/termine_type_all_20.svg' AS icon,
                        NULL AS date,
                        CONCAT('Link: ', name) AS title,
                        url AS text,
                        0.9 AS time_relevance
                    FROM links
                    WHERE
                        on_off = '1'
                        AND {$links_where}
                UNION ALL
                    {$static_page_query['query']}
                ZZZZZZZZZZ,
        ];
    }

    public function getPageTitle(): string {
        return "Service";
    }

    public function getPageDescription(): string {
        return "Diverse Online-Tools rund um OL und die OL Zimmerberg.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzServiceParams::class);

        $out = OlzHeader::render([
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
        ]);

        $out .= "<div class='content-full'>";

        $out .= "<h1>Service</h1>";
        $out .= "<h2>Apps</h2>";
        $out .= OlzAppsList::render();
        $out .= "<br /><br />";

        $out .= "<div class='responsive-flex'>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzLinks::render();
        $out .= "</div>";
        $out .= "<div class='responsive-flex-2'>";
        $out .= OlzDownloads::render();
        $out .= "</div></div><br><br>";

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
