<?php

namespace Olz\Apps\Youtube\Components\OlzYoutube;

use Olz\Apps\Youtube\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

class OlzYoutube extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams([]);
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
