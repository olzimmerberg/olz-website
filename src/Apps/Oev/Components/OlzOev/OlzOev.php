<?php

namespace Olz\Apps\Oev\Components\OlzOev;

use Olz\Apps\Oev\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\DbUtils;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzOev extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/config/paths.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $db = DbUtils::fromEnv()->getDb();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([
            'nach' => new FieldTypes\StringField(['allow_null' => true]),
            'ankunft' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $id = $_GET['id'] ?? null;

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "ÖV-Tool",
            'description' => "Tool für die Suche von gemeinsamen ÖV-Verbindungen.",
        ]);

        $out .= "<div class='content-full'>";

        $has_access = $this->authUtils()->hasPermission('any');
        if ($has_access) {
            $out .= <<<'ZZZZZZZZZZ'
            <div id='oev-root'></div>
            ZZZZZZZZZZ;
        } else {
            $out .= <<<'ZZZZZZZZZZ'
            <div id='oev-message' class='alert alert-danger' role='alert'>
                Da musst du schon eingeloggt sein!
            </div>
            ZZZZZZZZZZ;
        }

        $out .= "</div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();
        return $out;
    }
}
