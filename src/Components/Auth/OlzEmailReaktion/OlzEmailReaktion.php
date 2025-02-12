<?php

namespace Olz\Components\Auth\OlzEmailReaktion;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Utils\EmailUtils;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{token?: ?string}> */
class OlzEmailReaktionParams extends HttpParams {
}

/** @extends OlzComponent<array<string, mixed>> */
class OlzEmailReaktion extends OlzComponent {
    public function getHtml(mixed $args): string {
        $params = $this->httpUtils()->validateGetParams(OlzEmailReaktionParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $token = $params['token'] ?? '';
        $js_token = htmlentities(json_encode($token));
        $reaction_data = EmailUtils::fromEnv()->decryptEmailReactionToken($token);

        $out = OlzHeader::render([
            'title' => "Reaktion auf E-Mail",
            'description' => "Reaktion auf E-Mail.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        if ($reaction_data) {
            $question = null;
            if ($reaction_data['action'] == 'unsubscribe') {
                if (($reaction_data['notification_type'] ?? null) !== null) {
                    $question = "<p>Willst du wirklich <b>alle E-Mail dieser Art abbestellen?</b></p>";
                } elseif (isset($reaction_data['notification_type_all'])) {
                    $question = "<p>Willst du wirklich <b>jegliche E-Mails von OL Zimmerberg abbestellen?</b></p>";
                } else {
                    $question = "<p>Hier ist etwas falsch gelaufen! Dies ist eine unbekannte Aktion. Trotzdem probieren?</p>";
                }
            }
            if ($reaction_data['action'] == 'reset_password') {
                $question = "<p>Willst du wirklich <b>dein Passwort zurücksetzen?</b></p>";
            }
            if ($reaction_data['action'] == 'verify_email') {
                $question = "<p>Willst du <b>deine E-Mail-Adresse bestätigen?</b></p>";
            }
            if ($reaction_data['action'] == 'delete_news') {
                $question = "<p>Willst du wirklich <b>deinen anonymen Forumseintrag löschen?</b></p>";
            }
            if ($question) {
                $out .= <<<ZZZZZZZZZZ
                    {$question}
                    <p>
                        <a
                            class='btn btn-secondary'
                            href='{$code_href}'
                            role='button'
                        >
                            Abbrechen
                        </a>
                        <button
                            id='execute-reaction-button'
                            class='btn btn-danger'
                            type='submit'
                            onclick='olz.olzExecuteEmailReaction({$js_token})'
                        >
                            Ausführen
                        </button>
                    </p>
                    <div id='email-reaction-success-message' class='alert alert-success' role='alert'></div>
                    <div id='email-reaction-error-message' class='alert alert-danger' role='alert'></div>
                    ZZZZZZZZZZ;
            } else {
                $out .= "<div class='alert alert-danger' role='alert'>Ungültiger Link!</div>";
            }
        } else {
            $out .= "<div class='alert alert-danger' role='alert'>Ungültiger Link!</div>";
        }

        $out .= "</div>";

        $out .= OlzFooter::render();
        return $out;
    }
}
