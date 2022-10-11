<?php

namespace Olz\Apps\Youtube\Components\OlzYoutube;

use Olz\Apps\Youtube\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

class OlzYoutube {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        $logger = LogsUtils::fromEnv()->getLogger('Youtube');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
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
