<?php

namespace Olz;

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
