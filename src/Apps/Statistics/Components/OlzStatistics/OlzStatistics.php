<?php

namespace Olz\Apps\Statistics\Components\OlzStatistics;

use Olz\Apps\Statistics\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

class OlzStatistics {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        $logger = LogsUtils::fromEnv()->getLogger('statistics');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($logger);
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Statistics",
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
