<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use PhpTypeScriptApi\Fields\FieldTypes;
use Symfony\Component\Mime\Email;

class ResetPasswordEndpoint extends OlzEndpoint {
    public static function getIdent(): string {
        return 'ResetPasswordEndpoint';
    }

    public function getResponseField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'DENIED',
                'ERROR',
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField(): FieldTypes\Field {
        return new FieldTypes\ObjectField(['field_structure' => [
            'usernameOrEmail' => new FieldTypes\StringField([]),
            'recaptchaToken' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle(mixed $input): mixed {
        $username_or_email = trim($input['usernameOrEmail']);
        $user = $this->authUtils()->resolveUsernameOrEmail($username_or_email);
        if (!$user) {
            $this->log()->notice("Password reset for unknown user: {$username_or_email}.");
            return ['status' => 'DENIED'];
        }

        $token = $input['recaptchaToken'];
        if (!$this->recaptchaUtils()->validateRecaptchaToken($token)) {
            return ['status' => 'DENIED'];
        }

        $user_id = $user->getId();
        $new_password = $this->getRandomPassword();
        $reset_password_token = urlencode($this->emailUtils()->encryptEmailReactionToken([
            'action' => 'reset_password',
            'user' => $user_id,
            'new_password' => $new_password,
        ]));
        $base_url = $this->envUtils()->getBaseHref();
        $code_href = $this->envUtils()->getCodeHref();
        $reset_password_url = "{$base_url}{$code_href}email_reaktion?token={$reset_password_token}";
        $text = <<<ZZZZZZZZZZ
            **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**

            Hallo {$user->getFirstName()},

            *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier]({$reset_password_url}}) oder auf folgenden Link:

            {$reset_password_url}

            Dein neues Passwort lautet dann nachher:
            `{$new_password}`

            ZZZZZZZZZZ;
        $config = [
            'no_unsubscribe' => true,
        ];

        try {
            $email = (new Email())->subject("[OLZ] Passwort zurücksetzen");
            $email = $this->emailUtils()->buildOlzEmail($email, $user, $text, $config);
            $this->mailer->send($email);
            $this->log()->info("Password reset email sent to user ({$user_id}).");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->log()->critical("Error sending password reset email to user ({$user_id}): {$message}");
            return ['status' => 'ERROR'];
        }

        return ['status' => 'OK'];
    }

    protected function getRandomPassword(): string {
        return $this->generalUtils()->base64EncodeUrl(openssl_random_pseudo_bytes(6));
    }
}
