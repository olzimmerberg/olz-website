<?php

namespace Olz\Apps\Import\Components\OlzImport;

use Olz\Apps\Import\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;

class OlzImport extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Import",
            'norobots' => true,
        ]);

        $user = $this->authUtils()->getCurrentUser();
        $metadata = new Metadata();

        $out .= "<div class='content-full'>";
        if ($this->authUtils()->hasPermission('termine')) {
            $out .= <<<'ZZZZZZZZZZ'
            <div id='pastebox' class='dropzone' contenteditable='true'>Zellen aus Excel kopieren und hier einfügen.</div>
            ZZZZZZZZZZ;
        } else {
            $out .= OlzNoAppAccess::render([
                'app' => $metadata,
            ]);
        }
        $out .= "</div>";

        $out .= $metadata->getJsCssImports();
        $out .= OlzFooter::render();

        return $out;
    }
}
