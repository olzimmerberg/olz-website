<?php

namespace Olz\Components\Auth\OlzKontoStrava;

use Olz\Components\Auth\OlzProfileForm\OlzProfileForm;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzKontoStrava extends OlzComponent {
    public function getHtml($args = []): string {
        $params = $this->httpUtils()->validateGetParams([
            'code' => new FieldTypes\StringField(['allow_null' => true]),
            'scope' => new FieldTypes\StringField(['allow_null' => true]),
        ]);
        $code = $params['code'];

        $out = OlzHeader::render([
            'title' => "Strava Konto",
            'description' => "OLZ-Login mit Strava.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>
        <div>";

        $js_code = json_encode($code);

        $out .= <<<ZZZZZZZZZZ
            <script>olz.olzKontoLoginWithStrava({$js_code})</script>
            <div id='sign-up-with-strava-login-status' class='alert alert-secondary'>Login mit Strava wird gestartet...</div>
            <form
                id='sign-up-with-strava-form'
                class='default-form hidden'
                autocomplete='off'
                onsubmit='return olz.olzKontoSignUpWithStrava(this)'
            >
                <div class='success-message alert alert-success' role='alert'></div>
                <input
                    type='hidden'
                    name='strava-user'
                />
                <input
                    type='hidden'
                    name='access-token'
                />
                <input
                    type='hidden'
                    name='refresh-token'
                />
                <input
                    type='hidden'
                    name='expires-at'
                />
            ZZZZZZZZZZ;
        $out .= OlzProfileForm::render([
            'country_code' => 'CH',
        ]);
        $out .= <<<'ZZZZZZZZZZ'
                <button type='submit' class='btn btn-primary'>Konto erstellen</button>
                <div class='error-message alert alert-danger' role='alert'></div>
            </form>
            ZZZZZZZZZZ;

        $out .= "</div>
        </div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
