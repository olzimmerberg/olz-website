<?php

namespace Olz\Apps\Files\Components\OlzFiles;

use Olz\Apps\Files\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzFilesParams extends HttpParams {
}

class OlzFiles extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams(OlzFilesParams::class);
        $base_href = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();

        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            $this->httpUtils()->dieWithHttpError(401);
        }
        $user_root = $user ? $user->getRoot() : '';
        if (!$user_root) {
            $this->httpUtils()->dieWithHttpError(403);
        }

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Dateien",
            'norobots' => true,
        ]);

        $iframe_url = "{$base_href}/apps/files/artgris/?conf=default&tree=0";

        $out .= <<<ZZZZZZZZZZ
            <div class='content-full'>
                <iframe class='files-iframe' src='{$iframe_url}'></iframe>
            </div>
            ZZZZZZZZZZ;

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
