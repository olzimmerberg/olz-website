<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024Masks;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzPanini2024MasksParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzPanini2024Masks extends OlzComponent {
    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzPanini2024MasksParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
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
