<?php

namespace Olz\Apps\Oev\Components\OlzOev;

use Olz\Apps\Oev\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzOev extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams([
            'nach' => new FieldTypes\StringField(['allow_null' => true]),
            'ankunft' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $code_href = $this->envUtils()->getCodeHref();
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "ÖV-Tool",
            'description' => "Tool für die Suche von gemeinsamen ÖV-Verbindungen.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        $has_access = $this->authUtils()->hasPermission('any');
        if ($has_access) {
            $out .= <<<'ZZZZZZZZZZ'
                <div id='oev-root'></div>
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
