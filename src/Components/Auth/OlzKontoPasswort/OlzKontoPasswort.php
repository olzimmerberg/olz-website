<?php

namespace Olz\Components\Auth\OlzKontoPasswort;

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;

class OlzKontoPasswort extends OlzComponent {
    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $code_href = $this->envUtils()->getCodeHref();

        $out = OlzHeader::render([
            'title' => "OLZ-Konto mit Passwort",
            'description' => "OLZ-Login mit Passwort.",
            'norobots' => true,
        ]);

        $user = $this->authUtils()->getCurrentUser();

        $title = $user ? "Neues Familienmitglied" : "OLZ-Konto erstellen";
        $defaults = $user ? [
            'street' => $user->getStreet() ?? '',
            'postal_code' => $user->getPostalCode() ?? '',
            'city' => $user->getCity() ?? '',
            'region' => $user->getRegion() ?? '',
            'country_code' => $user->getCountryCode() ?? '',
        ] : [];

        $out .= "<div class='content-full'>
        <div>";

        if ($user) {
            $out .= <<<'ZZZZZZZZZZ'
            <div class='alert alert-danger' role='alert'>
                <b>Änderungen dürfen nur mit der Einwilligung der betreffenden Person vorgenommen werden!</b>
            </div>
            ZZZZZZZZZZ;
        }

        $out .= <<<ZZZZZZZZZZ
        <h1>{$title}</h1>
        <p><b>Wir behandeln deine Daten vertraulich und verwenden sie sparsam</b>: <a href='{$code_href}datenschutz' class='linkint' target='_blank'>Datenschutz</a></p>
        <p><span class='required-field-asterisk'>*</span> Zwingend notwendige Felder sind mit einem roten Sternchen gekennzeichnet.</p>
        <form
            id='sign-up-with-password-form'
            class='default-form'
            autocomplete='off'
            onsubmit='return olz.olzKontoSignUpWithPassword(this)'
        >
            <div class='success-message alert alert-success' role='alert'></div>
        ZZZZZZZZZZ;
        $out .= OlzProfileForm::render([
            'show_avatar' => false,
            'required_email' => $user ? false : true,
            'show_password' => true,
            'required_password' => $user ? false : true,
            ...$defaults,
        ]);
        $out .= <<<ZZZZZZZZZZ
            <p><input type='checkbox' name='recaptcha-consent-given' onchange='olz.olzSignUpRecaptchaConsent(this.checked)'> <span class='required-field-asterisk'>*</span> Ich akzeptiere, dass beim Erstellen des Kontos einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.</p>
            <p><input type='checkbox' name='cookie-consent-given'> <span class='required-field-asterisk'>*</span> Ich nehme zur Kenntnis, dass bei jedem Login notgedrungen ein Cookie in meinem Browser gesetzt wird. <a href='{$code_href}datenschutz' target='_blank'>Weitere Informationen zum Datenschutz</a></p>
            <button id='sign-up-with-password-submit-button' type='submit' class='btn btn-primary'>Konto erstellen</button>
            <div class='error-message alert alert-danger' role='alert'></div>
        </form>
        ZZZZZZZZZZ;

        $out .= "</div>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
