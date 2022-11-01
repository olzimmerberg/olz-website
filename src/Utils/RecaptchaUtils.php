<?php

namespace Olz\Utils;

use Olz\Fetchers\GoogleFetcher;

class RecaptchaUtils {
    use WithUtilsTrait;
    public const UTILS = [
        'envUtils',
        'log',
        'server',
    ];

    public static function fromEnv() {
        $google_fetcher = new GoogleFetcher();
        $instance = new self();
        $instance->setGoogleFetcher($google_fetcher);
        return $instance;
    }

    public function setGoogleFetcher($google_fetcher) {
        $this->googleFetcher = $google_fetcher;
    }

    public function validateRecaptchaToken(string $token): bool {
        $verification = $this->googleFetcher->fetchRecaptchaVerification([
            'secret' => $this->envUtils()->getRecaptchaSecretKey(),
            'response' => $token,
            'remoteip' => $this->server()['REMOTE_ADDR'],
        ]);
        $success = $verification['success'] ?? null;
        if ($success === null) {
            $this->log()->error("reCaptcha verification error.");
            return false;
        }
        if (!$success) {
            $this->log()->notice("reCaptcha denied.");
            return false;
        }
        return true;
    }
}
