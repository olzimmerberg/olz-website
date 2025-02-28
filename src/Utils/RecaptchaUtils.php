<?php

namespace Olz\Utils;

use Olz\Fetchers\GoogleFetcher;

class RecaptchaUtils {
    use WithUtilsTrait;

    /** @var array<string, bool> */
    protected static array $cache = [];

    protected ?GoogleFetcher $googleFetcher = null;

    public static function fromEnv(): self {
        $google_fetcher = new GoogleFetcher();
        $instance = new self();
        $instance->setGoogleFetcher($google_fetcher);
        return $instance;
    }

    public function setGoogleFetcher(GoogleFetcher $google_fetcher): void {
        $this->googleFetcher = $google_fetcher;
    }

    public function validateRecaptchaToken(?string $token): bool {
        if (!$token) {
            $this->log()->error("No reCaptcha token provided.");
            return false;
        }
        $app_env = $this->envUtils()->getAppEnv();
        if ($app_env === 'dev') {
            $this->log()->notice("Accept recaptcha, because env is '{$app_env}'");
            return true;
        }
        $cached = self::$cache[$token] ?? null;
        if ($cached !== null) {
            $this->log()->info("Using cached recaptcha response...");
            return $cached;
        }
        $this->generalUtils()->checkNotNull($this->googleFetcher, "No google fetcher");
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
