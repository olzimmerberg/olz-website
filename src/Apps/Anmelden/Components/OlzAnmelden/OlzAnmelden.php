<?php

namespace Olz\Apps\Anmelden\Components\OlzAnmelden;

use Olz\Apps\Anmelden\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzAnmelden extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $db = DbUtils::fromEnv()->getDb();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
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
