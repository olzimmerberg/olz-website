<?php

namespace Olz\Apps\Oev\Components\OlzOev;

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzOev {
    public static function render() {
        global $db;
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/config/database.php';
        require_once __DIR__.'/../../../../../_/config/paths.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $logger = LogsUtils::fromEnv()->getLogger(basename(__FILE__));
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([
            'nach' => new FieldTypes\StringField(['allow_null' => true]),
            'ankunft' => new FieldTypes\StringField(['allow_null' => true]),
        ], $_GET);

        $id = $_GET['id'] ?? null;

        echo OlzHeader::render([
            'title' => "ÖV-Tool",
            'description' => "Tool für die Suche von gemeinsamen ÖV-Verbindungen.",
        ]);

        echo "<div id='content_double'>";

        $auth_utils = AuthUtils::fromEnv();
        $has_access = $auth_utils->hasPermission('any');
        if ($has_access) {
            echo <<<'ZZZZZZZZZZ'
            <div id='oev-root'></div>
            <script>initOlzTransportConnectionSearch();</script>
            ZZZZZZZZZZ;
        } else {
            echo <<<'ZZZZZZZZZZ'
            <div id='oev-message' class='alert alert-danger' role='alert'>
                Da musst du schon eingeloggt sein!
            </div>
            ZZZZZZZZZZ;
        }

        echo "</div>";

        echo OlzFooter::render();
    }
}
