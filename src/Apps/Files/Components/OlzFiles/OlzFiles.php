<?php

namespace Olz\Apps\Files\Components\OlzFiles;

use Olz\Apps\Files\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;

class OlzFiles extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);

        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            HttpUtils::fromEnv()->dieWithHttpError(401);
        }
        $user_root = $user ? $user->getRoot() : '';
        if (!$user_root) {
            HttpUtils::fromEnv()->dieWithHttpError(403);
        }

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Dateien",
            'norobots' => true,
        ]);

        $iframe_url = "{$base_href}/apps/files/artgris/?conf=default&tree=0";

        // TODO: Remove link to old view
        $out .= <<<ZZZZZZZZZZ
        <div class='content-full'>
            <div><a href='/webftp.php' class='linkint'>Zurück zur alten Ansicht</a></div>
            <iframe class='files-iframe' src='{$iframe_url}'></iframe>
        </div>
        ZZZZZZZZZZ;

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
