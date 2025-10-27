<?php

namespace Olz\Components\Error\OlzOtherError;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;

/** @extends OlzComponent<array<string, mixed>> */
class OlzOtherError extends OlzComponent {
    public function getHtml(mixed $args): string {
        $http_status_code = $args['http_status_code'] ?? 500;
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler {$http_status_code}",
            'skip_auth_menu' => true,
        ], $this);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
            <div class='error-image-container-xxx'>
                <img
                    srcset='
                        {$code_href}assets/icns/error_system@2x.jpg 2x,
                        {$code_href}assets/icns/error_system.jpg 1x
                    '
                    src='{$code_href}assets/icns/error_system.jpg'
                    alt='Fehlerhafter Posten'
                    class='error-image-xxx'
                />
            </div>
            <h1>Fehler {$http_status_code}: Es ist ein unbekannter Fehler aufgetreten.</h1>
            <p><b>Hier ist dem Bahnleger ein peinlicher Fehler unterlaufen!</b></p>
            <p>Alle Karten müssen nachgedruckt werden!</p>
            <p>Bitte lass den Bahnleger unverzüglich wissen, dass hier ein Problem vorliegt:
            <script type='text/javascript'>
                olz.MailTo("website", "olzimmerberg.ch", "Bahnleger", "Fehler%20{$http_status_code}%20OLZ");
            </script></p>
            <p>In der Zwischenzeit kannst du dir <a href='{$code_href}' class='linkint'>am Start ein wenig die Beine vertreten</a>, oder es später nochmals versuchen.</p>
            ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
