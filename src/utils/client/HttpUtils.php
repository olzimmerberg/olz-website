<?php

require_once __DIR__.'/../../config/vendor/autoload.php';

class HttpUtils {
    use Psr\Log\LoggerAwareTrait;

    protected $fieldUtils;

    public function setFieldUtils($new_field_utils) {
        $this->fieldUtils = $new_field_utils;
    }

    public function dieWithHttpError(int $http_status_code) {
        $this->sendHttpResponseCode($http_status_code);

        $out = "";
        require_once __DIR__.'/../../components/page/olz_header/olz_header.php';
        $out .= olz_header_without_routing([
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
            <p>Aber keine Bange, <a href='index.php' class='linkint'>hier kannst du dich wieder auffangen.</a></p>
            <p>Und wenn du felsenfest davon überzeugt bist, dass der Posten hier sein <b>muss</b>, dann hat wohl der Postensetzer einen Fehler gemacht und sollte schläunigst informiert werden:
            <script type='text/javascript'>
                MailTo("olz_uu_01", "olzimmerberg.ch", "Postensetzer", "Fehler%20{$http_status_code}%20OLZ");
            </script></p>
        </div>
        ZZZZZZZZZZ;

        require_once __DIR__.'/../../components/page/olz_footer/olz_footer.php';
        $out .= olz_footer();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function redirect($redirect_url, $http_status_code = 301) {
        $this->sendHttpResponseCode($http_status_code);
        $this->sendHeader("Location: {$redirect_url}");

        $out = "";
        require_once __DIR__.'/../../components/page/olz_header/olz_header.php';
        $out .= olz_header_without_routing([
            'title' => "Weiterleitung...",
        ]);

        $out .= <<<ZZZZZZZZZZ
        <div id='content_double'>
            <h2>Automatische Weiterleitung...</h2>
            <p>Falls die automatische Weiterleitung nicht funktionieren sollte, kannst du auch diesenLink anklicken:</p>
            <p><b><a href='{$redirect_url}' class='linkint'>{$redirect_url}</a></b></p>
        </div>
        ZZZZZZZZZZ;

        require_once __DIR__.'/../../components/page/olz_footer/olz_footer.php';
        $out .= olz_footer();
        $this->sendHttpBody($out);
        $this->exitExecution();
    }

    public function validateGetParams($fields, $get_params) {
        $field_utils = FieldUtils::fromEnv();
        $validated_get_params = [];
        try {
            $validated_get_params = $field_utils->validate($fields, $get_params, ['parse' => true]);
        } catch (ValidationError $verr) {
            $this->logger->warning("Bad GET params", $verr->getStructuredAnswer());
            // TODO: Uncomment this, once we are sure we know all the GET variables.
            // $this->dieWithHttpError(400);
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

    public static function fromEnv() {
        $http_utils = new self();
        require_once __DIR__.'/../FieldUtils.php';
        $http_utils->setFieldUtils(FieldUtils::fromEnv());
        return $http_utils;
    }
}
