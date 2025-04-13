<?php

namespace Olz\Captcha\Utils;

use Olz\Utils\WithUtilsTrait;

/**
 * @phpstan-type OlzCaptchaConfig array{
 *   rand: string,
 *   date: string,
 *   mac: string,
 * }
 */
class CaptchaUtils {
    use WithUtilsTrait;

    /** @return OlzCaptchaConfig */
    public function generateOlzCaptchaConfig(int $length): array {
        // TODO: Derive from app secret?
        $key = $this->envUtils()->getEmailReactionKey();
        $rand = $this->getRandomString($length);
        $date = $this->dateUtils()->getIsoNow();
        $mac = $this->generalUtils()->hash($key, "{$date}{$rand}");
        return [
            'rand' => $rand,
            'date' => $date,
            'mac' => $mac,
        ];
    }

    public function getRandomString(int $length): string {
        $app_env = $this->envUtils()->getAppEnv();
        if ($app_env === 'dev') {
            return base64_encode(str_repeat('a', $length));
        }
        return base64_encode(openssl_random_pseudo_bytes($length));
    }

    public function validateToken(?string $token): bool {
        if (!$token) {
            $this->log()->error("No captcha token provided.");
            return false;
        }
        $app_env = $this->envUtils()->getAppEnv();
        if ($app_env === 'dev' && $token === 'dev') {
            $this->log()->notice("Accept '{$token}' captcha, because env is '{$app_env}'");
            return true;
        }

        $key = $this->envUtils()->getEmailReactionKey();
        $now = $this->dateUtils()->getIsoNow();

        $content = json_decode(base64_decode($token), true);
        $log = $content['log'] ?? [];
        $config = $content['config'] ?? [];
        $date = $config['date'] ?? '';
        $rand = $config['rand'] ?? '';
        $mac = $config['mac'] ?? '';
        $expected_mac = $this->generalUtils()->hash($key, "{$date}{$rand}");
        if ($mac !== $expected_mac) {
            $this->log()->info("Captcha denied: Invalid MAC of '{$date}{$rand}': {$config['mac']}", []);
            return false;
        }
        $validity_interval = \DateInterval::createFromDateString("+60 seconds");
        $end_of_validity = (new \DateTime($date))->add($validity_interval)->format('Y-m-d H:i:s');
        if ($end_of_validity < $now) {
            $this->log()->info("Captcha denied: End of validity was at {$end_of_validity}", []);
            return false;
        }
        $this->log()->info('Captcha result', [$log, $config]);
        return true;
    }
}
