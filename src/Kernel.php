<?php

namespace Olz;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

require_once __DIR__.'/OlzInit.php';

class Kernel extends BaseKernel {
    use MicroKernelTrait;
}
