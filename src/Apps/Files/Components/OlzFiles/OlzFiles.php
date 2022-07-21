<?php

namespace Olz\Apps\Files\Components\OlzFiles;

use Olz\Apps\Files\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

class OlzFiles {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        $env_utils = EnvUtils::fromEnv();
        $logger = $env_utils->getLogsUtils()->getLogger('files');
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLogger($logger);
        $http_utils->validateGetParams([], $_GET);

        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        if (!$user) {
            HttpUtils::fromEnv()->dieWithHttpError(401);
        }
        $user_root = $user ? $user->getRoot() : '';
        if (!$user_root) {
            HttpUtils::fromEnv()->dieWithHttpError(403);
        }

        $out = '';

        $out .= OlzHeader::render([
            'title' => "Dateien",
            'norobots' => true,
        ]);

        $base_href = $env_utils->getBaseHref();
        $iframe_url = "{$base_href}/apps/files/artgris/?conf=default&tree=0";

        $out .= <<<ZZZZZZZZZZ
        <div id='content_double'>
            <iframe class='files-iframe' src='{$iframe_url}'></iframe>
        </div>
        ZZZZZZZZZZ;

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
