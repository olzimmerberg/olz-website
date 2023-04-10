<?php

namespace Olz\Apps\Newsletter\Components\OlzNewsletter;

use Olz\Apps\Newsletter\Components\OlzEmailCard\OlzEmailCard;
use Olz\Apps\Newsletter\Components\OlzTelegramCard\OlzTelegramCard;
use Olz\Apps\Newsletter\Metadata;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

class OlzNewsletter extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';
        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Newsletter",
            'norobots' => true,
        ]);

        $user = $this->authUtils()->getCurrentUser();

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
            $out .= "<div id='profile-message' class='alert alert-danger' role='alert'>Kein Zugriff!</div>";
        }
        $out .= "</div>";

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
