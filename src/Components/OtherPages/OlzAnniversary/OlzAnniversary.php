<?php

namespace Olz\Components\OtherPages\OlzAnniversary;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\OtherPages\OlzAnniversaryRocket\OlzAnniversaryRocket;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzAnniversaryParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzAnniversary extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'Jubiläumsjahr';
    }

    public function getSearchResultsWhenHasAccess(array $terms): array {
        return [];
    }

    public static string $title = "🎉 20 Jahre OL Zimmerberg 🥳";
    public static string $description = "Alle Aktivitäten und Informationen zum Jubiläumsjahr 2026.";

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzAnniversaryParams::class);

        $out = OlzHeader::render([
            'title' => self::$title,
            'description' => self::$description,
        ]);
        $rocket = OlzAnniversaryRocket::render();
        $out .= <<<ZZZZZZZZZZ
            <div class='content-full'>
                <h1>🎉 20 Jahre OL Zimmerberg 🥳</h1>
                {$rocket}
                TODO
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        return $out;
    }
}
