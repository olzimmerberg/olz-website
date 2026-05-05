<?php

namespace Olz\Apps\SearchEngines\Components\OlzSearchEngines;

use Olz\Apps\SearchEngines\Metadata;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzSearchEnginesParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzSearchEngines extends OlzRootComponent {
    public function hasAccess(): bool {
        return (new Metadata())->isAccessibleToUser($this->authUtils()->getCurrentUser());
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $metadata = new Metadata();
        return $this->searchUtils()->getStaticResultQuery([
            'link' => $metadata->getHref(),
            'icon' => $metadata->getIconHref(),
            'title' => "Apps: {$this->getPageTitle()}",
            'text' => $this->getPageDescription(),
        ], $terms);
    }

    public function getPageTitle(): string {
        return "Suchmaschinen-Analyse";
    }

    public function getPageDescription(): string {
        return "Zugang zu den Suchmaschinen-Analyse-Tools.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzSearchEnginesParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => $this->getPageTitle(),
            'description' => $this->getPageDescription(),
            'norobots' => true,
        ]);

        $out .= <<<'ZZZZZZZZZZ'
            <style>
            .menu-container {
                max-width: none;
            } 
            .site-container {
                max-width: none;
            }
            </style>
            ZZZZZZZZZZ;

        $out .= "<div class='content-full'><div id='react-root'>Lädt...</div></div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
