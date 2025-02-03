<?php

namespace Olz\Apps\Anmelden\Components\OlzAnmelden;

use Olz\Apps\Anmelden\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{id?: ?numeric-string}> */
class OlzAnmeldenParams extends HttpParams {
}

/** @extends OlzComponent<array{id?: ?non-empty-string}> */
class OlzAnmelden extends OlzComponent {
    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzAnmeldenParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => 'Anmelden',
            'description' => "Hier kann man sich für OLZ-Anlässe anmelden.",
        ]);

        $out .= "<div class='content-full'><div id='react-root'>Lädt...</div></div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
