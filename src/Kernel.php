<?php

namespace Olz;

use Olz\Utils\EnvUtils;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel {
    use MicroKernelTrait;

    public function __construct(
        protected string $environment,
        protected bool $debug,
    ) {
        // Ampersand output
        ini_set('arg_separator.output', '&amp;');

        // Language for Date / Time output
        setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'de_DE.UTF8');

        // Character encoding
        mb_internal_encoding('UTF-8');

        // Time zone
        date_default_timezone_set('Europe/Zurich');

        // Session security
        if (!headers_sent()) {
            ini_set('session.gc_maxlifetime', 2419200); // keep one month
            ini_set('session.cookie_httponly', 1);
            try {
                $env_utils = EnvUtils::fromEnv();
                $private_path = $env_utils->getPrivatePath();
                if ($private_path !== null) {
                    session_save_path("{$private_path}sessions/");
                    ini_set('session.cookie_secure', 1);
                }
            } catch (\Throwable $th) {
                // ignore
            }
        }

        parent::__construct($environment, $debug);
    }

    public function getLogDir(): string {
        $private_path = $_ENV['PRIVATE_PATH'] ?? null;
        if ($private_path !== null) {
            return "{$this->getProjectDir()}/{$private_path}logs/";
        }
        return parent::getLogDir();
    }
}
