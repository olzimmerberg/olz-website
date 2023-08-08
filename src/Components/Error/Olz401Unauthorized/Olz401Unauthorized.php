<?php

namespace Olz\Components\Error\Olz401Unauthorized;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;

class Olz401Unauthorized extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler",
            'skip_auth_menu' => true,
            'skip_counter' => true,
        ], $this);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
        <div class='error-image-container-401'>
            <img
                srcset='
                    {$code_href}assets/icns/error_anonymous@2x.jpg 2x,
                    {$code_href}assets/icns/error_anonymous.jpg 1x
                '
                src='{$code_href}assets/icns/error_anonymous.jpg'
                alt='Anonymer Läufer'
                class='error-image-401'
            />
        </div>
        <h1>Fehler 401: Die gewünschte Seite ist nur für angemeldete Benutzer*innen.</h1>
        <p><b>Du hast vergessen, dich anzumelden!</b></p>
        <p>Du tauchst auf der Startliste einfach nicht auf.</p>
        <p>Aber keine Bange, <a href='#login-dialog' class='linkint'>hier kannst du dich nachmelden</a>.</p>
        <p>...und falls du noch kein OLZ-Konto besitzst, kannst du <a href='{$code_href}konto_passwort.php' class='linkint'>hier eins erstellen</a>.</p>
        ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([
            'skip_modals' => true,
        ], $this);

        return $out;
    }
}
