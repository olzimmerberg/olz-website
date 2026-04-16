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
        $audience = $args['audience'] ?? '';
        $offerings_by_audience = [
            'sportler' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotStarterpack,
                PredefinedSnippet::AngebotKleider,
            ],
            'schulen' => [
                PredefinedSnippet::AngebotDienstleistungen,
                PredefinedSnippet::AngebotMaterial,
                PredefinedSnippet::AngebotKarten,
            ],
            'organisatoren' => [
                PredefinedSnippet::AngebotKarten,
                PredefinedSnippet::AngebotMaterial,
            ],
            'einsteiger' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotStarterpack,
                PredefinedSnippet::AngebotKleider,
            ],
            'mitglieder' => [
                PredefinedSnippet::AngebotTrainings,
                PredefinedSnippet::AngebotStarterpack,
                PredefinedSnippet::AngebotKleider,
            ],
        ];
        $offerings = $offerings_by_audience[$audience] ?? null;

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);

        $out .= "<div class='content-full'>";
        $out .= "<h1>Angebot</h1>";

        if ($offerings !== null) {
            foreach ($offerings as $offering) {
                $out .= OlzEditableText::render(['snippet' => $offering]);
            }
        } else {
            $angebot_snippet_id = PredefinedSnippet::Angebot;
            $angebot_text = OlzEditableText::render(['snippet' => $angebot_snippet_id]);
            $out .= $angebot_text;
        }

        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
