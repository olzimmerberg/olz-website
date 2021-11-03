<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../OlzEndpoint.php';
require_once __DIR__.'/../../model/AuthRequest.php';
require_once __DIR__.'/../../model/User.php';

class ResetPasswordEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $_CONFIG, $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        require_once __DIR__.'/../../fetchers/GoogleFetcher.php';
        require_once __DIR__.'/../../utils/notify/EmailUtils.php';
        require_once __DIR__.'/../../utils/GeneralUtils.php';
        $email_utils = EmailUtils::fromEnv();
        $general_utils = GeneralUtils::fromEnv();
        $google_fetcher = new GoogleFetcher();
        $this->setEmailUtils($email_utils);
        $this->setEntityManager($entityManager);
        $this->setEnvUtils($_CONFIG);
        $this->setGeneralUtils($general_utils);
        $this->setGoogleFetcher($google_fetcher);
    }

    public function setEmailUtils($emailUtils) {
        $this->emailUtils = $emailUtils;
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public function setEnvUtils($envUtils) {
        $this->envUtils = $envUtils;
    }

    public function setGeneralUtils($generalUtils) {
        $this->generalUtils = $generalUtils;
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
            return ['status' => 'DENIED'];
        }

        $verification = $this->googleFetcher->fetchRecaptchaVerification([
            'secret' => $this->envUtils->getRecaptchaSecretKey(),
            'response' => $token,
            // 'remoteip' => // TODO
        ]);
        $success = $verification['success'] ?? null;
        if ($success === null) {
            return ['status' => 'ERROR'];
        }
        if (!$success) {
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
