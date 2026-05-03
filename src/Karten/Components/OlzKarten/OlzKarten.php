<?php

namespace Olz\Karten\Components\OlzKarten;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Karten\Karte;
use Olz\Karten\Components\OlzKartenList\OlzKartenList;
use Olz\Repository\Snippets\PredefinedSnippet;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzKartenParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzKarten extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        $snippets_where = $this->searchUtils()->getSnippetsWhereSql([
            PredefinedSnippet::KartenVerkauf,
        ], $terms);
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
                        '{$code_href}karten' AS link,
                        '{$code_href}assets/icns/link_map_16.svg' AS icon,
                        NULL AS date,
                        'Karten' AS title,
                        IFNULL(text, '') AS text,
                        1.0 AS time_relevance
                    FROM snippets
                    WHERE {$snippets_where}
                UNION ALL
                    {$static_page_query['query']}
                ZZZZZZZZZZ,
        ];
    }

    public function getPageTitle(): string {
        return "Unsere OL-Karten";
    }

    public function getPageDescription(): string {
        return "Die OL-Karten, die die OL Zimmerberg aufnimmt, unterhält und verkauft.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzKartenParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
        ]);

        $out .= "<div class='content-right olz-karten'>";
        $out .= OlzKartenList::render([]);
        $out .= "</div>
        <div class='content-middle olz-karten'>
        <h1>Unsere OL-Karten</h1>";

        $karten_repo = $this->entityManager()->getRepository(Karte::class);
        $karten = $karten_repo->findBy(['on_off' => 1]);
        $karten_data = array_map(function (Karte $karte) use ($code_href) {
            $icon_by_type = [
                'ol' => 'orienteering_forest_16.svg',
                'stadt' => 'orienteering_village_16.svg',
                'scool' => 'orienteering_scool_16.svg',
            ];
            return [
                'id' => $karte->getId(),
                'url' => "{$code_href}karten/{$karte->getId()}",
                'icon' => $icon_by_type[$karte->getKind()] ?? 'orienteering_scool_16.svg',
                'name' => $karte->getName(),
                'lat' => $karte->getLatitude(),
                'lng' => $karte->getLongitude(),
            ];
        }, $karten);
        $karten_json = json_encode($karten_data);

        $out .= <<<ZZZZZZZZZZ
            <div id='olz-karten-map' class='test-flaky'></div>
            <script>olz.olzKartenMapRender({$karten_json});</script>
            <br>
            ZZZZZZZZZZ;

        $out .= "<h2>Kartenverkauf</h2>";
        $out .= OlzEditableText::render(['snippet' => PredefinedSnippet::KartenVerkauf]);
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
