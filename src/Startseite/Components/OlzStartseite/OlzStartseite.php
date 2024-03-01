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

class OlzStartseite extends OlzComponent {
    public static $title = "Startseite";
    public static $description = "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.";

    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);

        $out = OlzHeader::render([
            'description' => self::$description,
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
