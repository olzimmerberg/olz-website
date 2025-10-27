<?php

namespace Olz\Apps\Oev\Components\OlzOev;

use Olz\Apps\Oev\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{
 *   nach?: ?string,
 *   ankunft?: ?string,
 * }> */
class OlzOevParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzOev extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResultsWhenHasAccess(array $terms): array {
        return [];
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzOevParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "ÖV-Tool",
            'description' => "Tool für die Suche von gemeinsamen ÖV-Verbindungen.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        $has_access = $this->authUtils()->hasPermission('any');
        if ($has_access) {
            $out .= <<<'ZZZZZZZZZZ'
                <div id='oev-root'></div>
                ZZZZZZZZZZ;
        } else {
            $out .= OlzNoAppAccess::render([
                'app' => $metadata,
            ]);
        }

        $out .= "</div>";

        $out .= $metadata->getJsCssImports();
        $out .= OlzFooter::render();

        return $out;
    }
}
