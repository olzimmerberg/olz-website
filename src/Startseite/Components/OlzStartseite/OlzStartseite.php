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
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzStartseiteParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzStartseite extends OlzComponent {
    public static string $title = "Startseite";
    public static string $description = "Eine Übersicht der Neuigkeiten und geplanten Anlässe der OL Zimmerberg.";

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzStartseiteParams::class);

        $out = OlzHeader::render([
            'description' => self::$description,
        ], $this);

        $out .= "<div class='content-full'>";

        $banner_text = OlzEditableText::render(['snippet_id' => 22], $this);
        if (trim(strip_tags($banner_text)) !== '' || $this->authUtils()->hasPermission('olz_text_22')) {
            $out .= "<div id='important-banner' class='banner'>";
            $out .= $banner_text;
            $out .= "</div>";
        }

        $out .= OlzCustomizableHome::render([], $this);

        $out .= "</div>";

        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
