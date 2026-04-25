<?php

namespace Olz\Captcha\Components\OlzEmailModal;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array{
 *   email: non-empty-string,
 *   text?: ?non-empty-string,
 *   subject?: ?non-empty-string,
 * }> */
class OlzEmailModal extends OlzComponent {
    public function getHtml(mixed $args): string {
        $text = $args['text'] ?? 'E-Mail';
        $key = $this->envUtils()->getEncryptionKey('email-captcha');
        $data = [
            'email' => $args['email'],
            'text' => strip_tags($text),
            'subject' => $args['subject'] ?? null,
        ];
        $email_token = $this->generalUtils()->encrypt($key, $data);
        $enc_email_token = json_encode($email_token);
        return <<<ZZZZZZZZZZ
            <a
                href='#'
                onclick='return olz.initOlzEmailModal({$enc_email_token})'
                class='linkmail'
            >
                {$text}
            </a>
            ZZZZZZZZZZ;
    }
}
