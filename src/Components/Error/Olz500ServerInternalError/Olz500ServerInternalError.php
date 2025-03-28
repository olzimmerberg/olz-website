<?php

namespace Olz\Components\Error\Olz500ServerInternalError;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;

/** @extends OlzComponent<array<string, mixed>> */
class Olz500ServerInternalError extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler",
            'skip_auth_menu' => true,
        ], $this);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
            <div class='error-image-container-500'>
                <img
                    srcset='
                        {$code_href}assets/icns/error_system@2x.jpg 2x,
                        {$code_href}assets/icns/error_system.jpg 1x
                    '
                    src='{$code_href}assets/icns/error_system.jpg'
                    alt='Fehlerhafter Posten'
                    class='error-image-500'
                />
            </div>
            <h1>Fehler 500: Es ist ein Fehler in unserem System aufgetreten.</h1>
            <p><b>Hier ist dem Bahnleger ein peinlicher Fehler unterlaufen!</b></p>
            <p>Alle Karten müssen nachgedruckt werden!</p>
            <p>Bitte lass den Bahnleger unverzüglich wissen, dass hier ein Problem vorliegt:
            <script type='text/javascript'>
                olz.MailTo("website", "olzimmerberg.ch", "Bahnleger", "Fehler%20500%20OLZ");
            </script></p>
            <p>In der Zwischenzeit kannst du dir <a href='{$code_href}' class='linkint'>am Start ein wenig die Beine vertreten</a>, oder es später nochmals versuchen.</p>
            ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
