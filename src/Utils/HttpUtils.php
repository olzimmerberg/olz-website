<?php

namespace Olz\Utils;

use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeaderWithoutRouting\OlzHeaderWithoutRouting;
use PhpTypeScriptApi\Fields\ValidationError;

class HttpUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'fieldUtils',
        'logger',
    ];

    public function dieWithHttpError(int $http_status_code) {
        $this->sendHttpResponseCode($http_status_code);

        $out = "";
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Fehler",
        ]);

        $out .= <<<ZZZZZZZZZZ
        <div id='content_rechts'>
            <h2>&nbsp;</h2>
            <img src='icns/schilf.jpg' style='width:98%;' alt=''>
        </div>
        <div id='content_mitte'>
            <h2>Fehler {$http_status_code}: Die gewünschte Seite konnte nicht gefunden werden.</h2>
            <p><b>Hier bist du voll im Schilf!</b></p>
            <p>Kein Posten weit und breit.</p>
            <p>Vielleicht hast du falsch abgezeichnet? Oder der Posten wurde bereits abgeräumt!</p>
            <p>Aber keine Bange, <a href='startseite.php' class='linkint'>hier kannst du dich wieder auffangen.</a></p>
            <p>Und wenn du felsenfest davon überzeugt bist, dass der Posten hier sein <b>muss</b>, dann hat wohl der Postensetzer einen Fehler gemacht und sollte schläunigst informiert werden:
            <script type='text/javascript'>
                MailTo("website", "olzimmerberg.ch", "Postensetzer", "Fehler%20{$http_status_code}%20OLZ");
            </script></p>
        </div>
        ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function redirect($redirect_url, $http_status_code = 301) {
        $this->sendHttpResponseCode($http_status_code);
        $this->sendHeader("Location: {$redirect_url}");

        $out = "";
        $out .= OlzHeaderWithoutRouting::render([
            'title' => "Weiterleitung...",
        ]);

        $out .= <<<ZZZZZZZZZZ
        <div id='content_double'>
            <h2>Automatische Weiterleitung...</h2>
            <p>Falls die automatische Weiterleitung nicht funktionieren sollte, kannst du auch diesenLink anklicken:</p>
            <p><b><a href='{$redirect_url}' class='linkint'>{$redirect_url}</a></b></p>
        </div>
        ZZZZZZZZZZ;

        $out .= OlzFooter::render();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function validateGetParams($fields, $get_params, $options = []) {
        $validated_get_params = [];
        $has_error = false;
        foreach ($get_params as $key => $value) {
            $field = $fields[$key] ?? null;
            if (!$field) {
                $this->logger->notice("Unknown GET param '{$key}'");
                $has_error = true;
            } else {
                try {
                    $validated_get_params[$key] = $this->fieldUtils->validate(
                        $field, $get_params[$key] ?? null, ['parse' => true]);
                } catch (ValidationError $verr) {
                    $this->logger->notice("Bad GET param '{$key}'", $verr->getStructuredAnswer());
                    $has_error = true;
                }
            }
        }
        if ($has_error && ($options['just_log'] ?? false) === false) {
            $this->dieWithHttpError(400);
        }
        return $validated_get_params;
    }

    // @codeCoverageIgnoreStart
    // Reason: Mock functions for tests.

    protected function sendHttpResponseCode($http_response_code) {
        http_response_code($http_response_code);
    }

    protected function sendHeader($http_header_line) {
        header($http_header_line);
    }

    protected function sendHttpBody($http_body) {
        echo $http_body;
    }

    protected function exitExecution() {
        exit('');
    }

    // @codeCoverageIgnoreEnd
}
