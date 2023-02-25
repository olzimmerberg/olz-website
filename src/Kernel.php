<?php

namespace Olz;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

require_once __DIR__.'/../_/config/init.php';

class Kernel extends BaseKernel {
    use MicroKernelTrait;
}
