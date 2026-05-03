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
        return (new Metadata())->isAccessibleToUser($this->authUtils()->getCurrentUser());
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $metadata = new Metadata();
        return $this->searchUtils()->getStaticResultQuery([
            'link' => $metadata->getHref(),
            'icon' => $metadata->getIconHref(),
            'title' => $this->getPageTitle(),
            'text' => $this->getPageDescription(),
        ], $terms);
    }

    public function getPageTitle(): string {
        return "Apps: öV";
    }

    public function getPageDescription(): string {
        return "Tool für die Suche von gemeinsamen ÖV-Verbindungen.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzOevParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
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
