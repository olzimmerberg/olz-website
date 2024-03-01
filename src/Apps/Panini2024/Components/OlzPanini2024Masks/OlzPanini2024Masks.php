<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024Masks;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpUtils;

class OlzPanini2024Masks extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);
        $metadata = new Metadata();

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Panini '24 Masks",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        if ($this->authUtils()->hasPermission('panini2024')) {
            $enc_mask = json_encode($args['mask'] ?? '');
            $out .= <<<ZZZZZZZZZZ
            <script>
                window.olzPanini2024Mask = {$enc_mask};
            </script>
            <div id='panini-react-root-masks'>
                Lädt...
            </div>
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
