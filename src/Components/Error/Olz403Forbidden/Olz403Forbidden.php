<?php

namespace Olz\Components\Error\Olz403Forbidden;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;

class Olz403Forbidden extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler",
            'skip_auth_menu' => true,
            'skip_counter' => true,
        ]);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
        <div class='error-image-container-403'>
            <img
                srcset='
                    {$code_href}icns/error_tape@2x.jpg 2x,
                    {$code_href}icns/error_tape.jpg 1x
                '
                src='{$code_href}icns/error_tape.jpg'
                alt='Anonymer Läufer'
                class='error-image-403'
            />
        </div>
        <h1>Fehler 403: Du hast keine Erlaubnis für die gewünschte Seite.</h1>
        <p><b>Au weia, voll mitten im Sperrgebiet ertappt!</b></p>
        <p>Schnell <a href='{$code_href}startseite.php' class='linkint'>weg hier, zurück zum Start</a>.</p>
        <p>Du hast eine Sondererlaubnis? Dann müsstest du dich evtl. ausloggen, und <a href='#login-dialog' class='linkint'>mit einem berechtigten Konto wieder einloggen</a>.</p>
        ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([
            'skip_modals' => true,
        ]);

        return $out;
    }
}
