<?php

namespace Olz\Captcha\Endpoints;

use Olz\Api\OlzTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzEmailInfoData  array{
 *   email: array<non-empty-string>,
 *   text: non-empty-string,
 *   subject?: ?non-empty-string,
 * }
 *
 * @extends OlzTypedEndpoint<
 *   array{
 *     emailToken: non-empty-string,
 *     captchaToken?: ?non-empty-string,
 *   },
 *   OlzEmailInfoData,
 * >
 */
class DecryptEmailTokenEndpoint extends OlzTypedEndpoint {
    protected function handle(mixed $input): mixed {
        $has_access = $this->authUtils()->hasPermission('any');
        $token = $input['captchaToken'] ?? null;
        $is_valid_token = $token ? $this->captchaUtils()->validateToken($token) : false;
        if (!$has_access && !$is_valid_token) {
            throw new HttpError(403, 'Captcha token invalid');
        }

        $key = $this->envUtils()->getEncryptionKey('email-captcha');
        $decrypted = $this->generalUtils()->decrypt($key, $input['emailToken']);
        $email = $decrypted['email'] ?? null;
        if (!$email) {
            throw new HttpError(400, 'Email token invalid');
        }
        return [
            'email' => $this->emailUtils()->obfuscateEmail($email) ?? [],
            'text' => $decrypted['text'] ?? 'E-Mail',
            'subject' => $decrypted['subject'] ?? null,
        ];
    }
}
