<?php

namespace Olz\Apps\Newsletter\Components\OlzNewsletter;

use Olz\Apps\Newsletter\Components\OlzEmailCard\OlzEmailCard;
use Olz\Apps\Newsletter\Components\OlzTelegramCard\OlzTelegramCard;
use Olz\Apps\Newsletter\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzNewsletterParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzNewsletter extends OlzComponent {
    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzNewsletterParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Newsletter",
            'norobots' => true,
        ]);

        $user = $this->authUtils()->getCurrentUser();
        $metadata = new Metadata();

        $out .= "<div class='content-full'>";
        if ($user) {
            $out .= "<div class='responsive-flex'>";
            $out .= "<div class='responsive-flex-2'>";
            $out .= OlzTelegramCard::render();
            $out .= "</div>";
            $out .= "<div class='responsive-flex-2'>";
            $out .= OlzEmailCard::render();
            $out .= "</div>";
            $out .= "</div>";
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
