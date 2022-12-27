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

    protected static $cache = [];

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
        $cached = self::$cache[$token] ?? null;
        if ($cached !== null) {
            $this->log()->info("Using cached recaptcha response...");
            return $cached;
        }
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
            self::$cache[$token] = false;
            return false;
        }
        self::$cache[$token] = true;
        return true;
    }
}
