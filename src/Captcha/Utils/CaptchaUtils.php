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
        $app_env = $this->getAppEnv();
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
        $app_env = $this->getAppEnv();
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
            $this->log()->info("Captcha denied: Invalid MAC of '{$date}{$rand}': {$mac}", []);
            return false;
        }
        $validity_interval = \DateInterval::createFromDateString("+60 seconds");
        $end_of_validity = (new \DateTime($date))->add($validity_interval)->format('Y-m-d H:i:s');
        if ($end_of_validity < $now) {
            $this->log()->info("Captcha denied: End of validity was at {$end_of_validity}", []);
            return false;
        }

        $bytes = base64_decode($rand) ?: '';
        $len = strlen($bytes);
        if ($len !== 3) {
            $this->log()->warning("Captcha denied: Rand length must be 3, was {$len}", []);
            return false;
        }
        $x_start = ord($bytes[0]) / 255.0 * 75 + 10;
        $y_bar = ord($bytes[1]) / 255.0 * 176 + 12;
        $target = round(2 + ord($bytes[2]) / 20.0);
        $x_end_min = $x_start + ($target - 0.55) / 15 * 280;
        $x_end_max = $x_start + ($target + 0.55) / 15 * 280;
        $context = [
            'log' => $log,
            'config' => $config,
            'x_start' => $x_start,
            'y_bar' => $y_bar,
            'target' => $target,
            'x_end_min' => $x_end_min,
            'x_end_max' => $x_end_max,
        ];
        $constraints = [false, false];
        for ($i = 0; $i < count($log); $i++) {
            $entry = $log[$i];
            $res = preg_match('/^(D|M|U)([0-9]+)\,([0-9]+)$/', $entry, $matches);
            if (!$res) {
                $this->log()->info("Captcha denied: Log[{$i}] = '{$entry}' does not match pattern", $context);
                return false;
            }
            $event = $matches[1];
            $x = $matches[2];
            $y = $matches[3];
            if ($event === 'D' && pow($x - $x_start, 2) + pow($y - $y_bar, 2) <= pow(12, 2)) {
                $constraints[0] = true;
            }
            if ($event === 'U' && $x >= $x_end_min && $x <= $x_end_max) {
                $constraints[1] = true;
            }
        }
        for ($i = 0; $i < count($constraints); $i++) {
            if (!$constraints[$i]) {
                $this->log()->info("Captcha denied: Constraint[{$i}] failed", $context);
                return false;
            }
        }
        $this->log()->info('Captcha succeeded', $context);
        return true;
    }

    protected function getAppEnv(): string {
        return $this->envUtils()->getAppEnv();
    }
}
