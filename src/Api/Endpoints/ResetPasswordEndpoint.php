<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\User;
use Olz\Fetchers\GoogleFetcher;
use PhpTypeScriptApi\Fields\FieldTypes;

class ResetPasswordEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        $google_fetcher = new GoogleFetcher();
        $this->setGoogleFetcher($google_fetcher);
    }

    public function setGoogleFetcher($new_google_fetcher) {
        $this->googleFetcher = $new_google_fetcher;
    }

    public static function getIdent() {
        return 'ResetPasswordEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'DENIED',
                'ERROR',
                'OK',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'usernameOrEmail' => new FieldTypes\StringField([]),
            'recaptchaToken' => new FieldTypes\StringField([]),
        ]]);
    }

    protected function handle($input) {
        $username_or_email = trim($input['usernameOrEmail']);
        $token = $input['recaptchaToken'];

        $user_repo = $this->entityManager->getRepository(User::class);
        $user = $user_repo->findOneBy(['username' => $username_or_email]);
        if (!$user) {
            $user = $user_repo->findOneBy(['email' => $username_or_email]);
        }
        if (!$user) {
            $this->logger->notice("Password reset for unknown user: {$username_or_email}.");
            return ['status' => 'DENIED'];
        }

        $verification = $this->googleFetcher->fetchRecaptchaVerification([
            'secret' => $this->envUtils->getRecaptchaSecretKey(),
            'response' => $token,
            'remoteip' => $this->server['REMOTE_ADDR'],
        ]);
        $success = $verification['success'] ?? null;
        if ($success === null) {
            $this->logger->notice("reCaptcha verification error.");
            return ['status' => 'ERROR'];
        }
        if (!$success) {
            $this->logger->notice("reCaptcha denied.");
            return ['status' => 'DENIED'];
        }

        $user_id = $user->getId();
        $new_password = $this->getRandomPassword();
        $reset_password_token = urlencode($this->emailUtils->encryptEmailReactionToken([
            'action' => 'reset_password',
            'user' => $user_id,
            'new_password' => $new_password,
        ]));
        $base_url = $this->envUtils->getBaseHref();
        $code_href = $this->envUtils->getCodeHref();
        $reset_password_url = "{$base_url}{$code_href}email_reaktion.php?token={$reset_password_token}";
        $text = <<<ZZZZZZZZZZ
        **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**

        Hallo {$user->getFirstName()},

        *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier]({$reset_password_url}}) oder auf folgenden Link:

        {$reset_password_url}

        Dein neues Passwort lautet dann:
        `{$new_password}`

        ZZZZZZZZZZ;
        $config = [
            'no_unsubscribe' => true,
        ];

        try {
            $this->emailUtils->setLogger($this->logger);
            $email = $this->emailUtils->createEmail();
            $email->configure($user, "[OLZ] Passwort zurücksetzen", $text, $config);
            $email->send();
            $this->logger->info("Password reset email sent to user ({$user_id}).");
        } catch (\Exception $exc) {
            $message = $exc->getMessage();
            $this->logger->critical("Error sending password reset email to user ({$user_id}): {$message}");
        }

        return ['status' => 'OK'];
    }

    protected function getRandomPassword() {
        return $this->generalUtils->base64EncodeUrl(openssl_random_pseudo_bytes(6));
    }
}
