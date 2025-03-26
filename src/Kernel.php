<?php

namespace Olz;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

require_once __DIR__.'/OlzInit.php';

class Kernel extends BaseKernel {
    use MicroKernelTrait;

    public function getLogDir(): string {
        $private_path = $_ENV['PRIVATE_PATH'] ?? null;
        if ($private_path !== null) {
            return "{$this->getProjectDir()}/{$private_path}logs/";
        }
        return parent::getLogDir();
    }
}
