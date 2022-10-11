<?php

namespace Olz\Apps\Anmelden\Components\OlzAnmelden;

use Olz\Apps\Anmelden\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzAnmelden {
    public static function render() {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/config/server.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $db = DbUtils::fromEnv()->getDb();
        $logger = LogsUtils::fromEnv()->getLogger('anmelden');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'title' => 'Anmelden',
            'description' => "Hier kann man sich für OLZ-Anlässe anmelden.",
        ]);

        $out .= "<div id='content_double'><div id='react-root'>Lädt...</div></div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
