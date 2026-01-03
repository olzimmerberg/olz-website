<?php

namespace Olz\Apps\Youtube\Components\OlzYoutube;

use Olz\Apps\Youtube\Metadata;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzYoutubeParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzYoutube extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function searchSqlWhenHasAccess(array $terms): ?string {
        return null;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzYoutubeParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Youtube-Kanal",
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
