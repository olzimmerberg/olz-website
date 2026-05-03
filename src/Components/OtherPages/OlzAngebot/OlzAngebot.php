<?php

namespace Olz\Components\OtherPages\OlzAngebot;

use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Repository\Snippets\PredefinedSnippet;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzAngebotParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzAngebot extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        $snippets_where = $this->searchUtils()->getSnippetsWhereSql([
            PredefinedSnippet::AngebotTrainings,
            PredefinedSnippet::AngebotStarterpack,
            PredefinedSnippet::AngebotKleider,
            PredefinedSnippet::AngebotKarten,
            PredefinedSnippet::AngebotMaterial,
            PredefinedSnippet::AngebotKurse,
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
                        '{$code_href}angebot' AS link,
                        '{$code_href}assets/icns/question_mark_20.svg' AS icon,
                        NULL AS date,
                        'Angebot' AS title,
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
        return "Angebot";
    }

    public function getPageDescription(): string {
        return "Unser Angebot an Material und Kleidern, die die OL Zimmerberg vermietet bzw. verkauft.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzAngebotParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $audience = $args['audience'] ?? '';
        $filter_text_by_audience = [
            'sportler' => 'für Sportler',
            'schulen' => 'für Schulen',
            'organisatoren' => 'für Organisatoren',
            'anfaenger' => 'für Anfänger',
            'mitglieder' => 'für Mitglieder',
        ];
        $default_offerings = [
            PredefinedSnippet::AngebotTrainings,
            PredefinedSnippet::AngebotStarterpack,
            PredefinedSnippet::AngebotKleider,
            PredefinedSnippet::AngebotKarten,
            PredefinedSnippet::AngebotMaterial,
            PredefinedSnippet::AngebotKurse,
        ];
        $offerings_by_audience = [
            'sportler' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotStarterpack,
                PredefinedSnippet::AngebotKleider,
            ],
            'schulen' => [
                PredefinedSnippet::AngebotKurse,
                PredefinedSnippet::AngebotMaterial,
                PredefinedSnippet::AngebotKarten,
            ],
            'organisatoren' => [
                PredefinedSnippet::AngebotKarten,
                PredefinedSnippet::AngebotMaterial,
            ],
            'anfaenger' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotStarterpack,
                PredefinedSnippet::AngebotKleider,
            ],
            'mitglieder' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotKleider,
            ],
            'trainings' => [PredefinedSnippet::AngebotTrainings],
            'starterpack' => [PredefinedSnippet::AngebotStarterpack],
            'kleider' => [PredefinedSnippet::AngebotKleider],
            'karten' => [PredefinedSnippet::AngebotKarten],
            'material' => [PredefinedSnippet::AngebotMaterial],
            'kurse' => [PredefinedSnippet::AngebotKurse],
        ];
        $filter_text = $filter_text_by_audience[$audience] ?? null;
        $pretty_filter = $filter_text ? " <span class='filter'>{$filter_text}</span>" : '';
        $offerings = $offerings_by_audience[$audience] ?? $default_offerings;

        $out = OlzHeader::render([
            'back_link' => $filter_text === null ? null : "{$code_href}",
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
        ]);

        $out .= "<div class='content-full olz-angebot'>";
        $out .= "<h1>Angebot</h1>{$pretty_filter}";

        foreach ($offerings as $offering) {
            $out .= OlzEditableText::render(['snippet' => $offering]);
        }

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
