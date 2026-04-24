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
        return null;
    }

    public static string $title = "Angebot";
    public static string $description = "Unser Angebot an Material und Kleidern, die die OL Zimmerberg vermietet bzw. verkauft.";

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
            'title' => self::$title,
            'description' => self::$description,
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
