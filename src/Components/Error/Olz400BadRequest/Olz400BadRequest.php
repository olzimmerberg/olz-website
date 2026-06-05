<?php

namespace Olz\Components\Error\Olz400BadRequest;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;
use Olz\Entity\Roles\Role;
use Olz\Repository\Roles\PredefinedRole;

/** @extends OlzComponent<array<string, mixed>> */
class Olz400BadRequest extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $out = '';
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler 400 Fehlerhafte Anfrage",
            'skip_auth_menu' => true,
        ], $this);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $sysadmin_role = $role_repo->getPredefinedRole(PredefinedRole::Sysadmin);
        $out .= "<div class='content-full'>";
        $out .= <<<ZZZZZZZZZZ
            <div class='error-image-container-400'>
                <img
                    srcset='
                        {$code_href}assets/icns/error_schilf@2x.jpg 2x,
                        {$code_href}assets/icns/error_schilf.jpg 1x
                    '
                    src='{$code_href}assets/icns/error_schilf.jpg'
                    alt='Schilf'
                    class='error-image-400'
                />
            </div>
            <h1>Fehler 400: Die Anfrage-Nachricht ist fehlerhaft aufgebaut.</h1>
            <p><b>Hier bist du voll im Schilf!</b></p>
            <p>Kein Posten weit und breit.</p>
            <p>Vielleicht hast du falsch abgezeichnet? Oder der Posten wurde bereits abgeräumt!</p>
            <p>Aber keine Bange, <a href='{$code_href}' class='linkint'>hier kannst du dich wieder auffangen.</a></p>
            <p>Und wenn du felsenfest davon überzeugt bist, dass der Posten hier sein <b>muss</b>, dann hat wohl der Postensetzer einen Fehler gemacht und sollte schläunigst informiert werden:
                <a
                    href='#'
                    onclick='return olz.initOlzRoleInfoModal({$sysadmin_role?->getId()})'
                    class='linkmail'
                >
                    Postensetzer
                </a>
            </p>
            ZZZZZZZZZZ;
        $out .= "</div>";
        $out .= OlzFooter::render([], $this);

        return $out;
    }
}
