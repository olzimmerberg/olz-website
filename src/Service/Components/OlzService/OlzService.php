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

    public function getSearchTitle(): string {
        return 'Service';
    }

    public function searchSqlWhenHasAccess(array $terms): ?string {
        $code_href = $this->envUtils()->getCodeHref();
        $downloads_where = implode(' AND ', array_map(function ($term) {
            return "name LIKE '%{$term}%'";
        }, $terms));
        $links_where = implode(' AND ', array_map(function ($term) {
            return "(name LIKE '%{$term}%' OR url LIKE '%{$term}%')";
        }, $terms));
        // TODO better icons
        return <<<ZZZZZZZZZZ
            SELECT
                '{$code_href}service' AS link,
                '{$code_href}assets/icns/link_internal_16.svg' AS icon,
                NULL AS date,
                name AS title,
                NULL AS text,
                0.7 AS time_relevance
            FROM downloads
            WHERE
                on_off = '1'
                AND {$downloads_where}
            UNION ALL
            SELECT
                '{$code_href}service' AS link,
                '{$code_href}assets/icns/termine_type_all_20.svg' AS icon,
                NULL AS date,
                name AS title,
                url AS text,
                0.7 AS time_relevance
            FROM links
            WHERE
                on_off = '1'
                AND {$links_where}
            ZZZZZZZZZZ;
    }

    public static string $title = "Service";
    public static string $description = "Diverse Online-Tools rund um OL und die OL Zimmerberg.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzServiceParams::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
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
