<?php

// =============================================================================
// Zeigt die OLZ Startseite an.
// =============================================================================

namespace Olz\Startseite\Components\OlzStartseite;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzEditableText\OlzEditableText;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Startseite\Components\OlzCustomizableHome\OlzCustomizableHome;
use Olz\Utils\HttpUtils;

class OlzStartseite extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);

        $out = '';

        $out .= OlzHeader::render([
            'description' => "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.",
        ], $this);

        $banner_text = OlzEditableText::render(['olz_text_id' => 22], $this);
        if (trim(strip_tags($banner_text)) !== '') {
            $out .= "<div class='content-full'><div id='important-banner' class='banner'>";
            $out .= $banner_text;
            $out .= "</div></div>";
        }

        $out .= OlzCustomizableHome::render([], $this);

        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
