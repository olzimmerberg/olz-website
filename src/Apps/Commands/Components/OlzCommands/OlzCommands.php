<?php

namespace Olz\Apps\Commands\Components\OlzCommands;

use Olz\Apps\Commands\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

class OlzCommands extends OlzComponent {
    public function getHtml($args = []): string {
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => 'Commands',
            'description' => "Symfony-Commands (Befehle) ausführen.",
        ]);

        $out .= "<div class='content-full'><div id='react-root'>Lädt...</div></div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
