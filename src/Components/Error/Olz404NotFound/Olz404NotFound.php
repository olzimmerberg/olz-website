<?php

namespace Olz\Components\Error\Olz404NotFound;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;

class Olz404NotFound extends OlzComponent {
    public function getHtml(array $args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler",
            'skip_auth_menu' => true,
        ], $this);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
            <div class='error-image-container-404'>
                <img
                    srcset='
                        {$code_href}assets/icns/error_schilf@2x.jpg 2x,
                        {$code_href}assets/icns/error_schilf.jpg 1x
                    '
                    src='{$code_href}assets/icns/error_schilf.jpg'
                    alt='Schilf'
                    class='error-image-404'
                />
            </div>
            <h1>Fehler 404: Die gewünschte Seite konnte nicht gefunden werden.</h1>
            <p><b>Hier bist du voll im Schilf!</b></p>
            <p>Kein Posten weit und breit.</p>
            <p>Vielleicht hast du falsch abgezeichnet? Oder der Posten wurde bereits abgeräumt!</p>
            <p>Aber keine Bange, <a href='{$code_href}' class='linkint'>hier kannst du dich wieder auffangen.</a></p>
            <p>Und wenn du felsenfest davon überzeugt bist, dass der Posten hier sein <b>muss</b>, dann hat wohl der Postensetzer einen Fehler gemacht und sollte schläunigst informiert werden:
            <script type='text/javascript'>
                olz.MailTo("website", "olzimmerberg.ch", "Postensetzer", "Fehler%20404%20OLZ");
            </script></p>
            ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
