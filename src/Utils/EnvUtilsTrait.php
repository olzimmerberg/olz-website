<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait EnvUtilsTrait {
    protected function envUtils(): EnvUtils {
        $util = WithUtilsCache::get('envUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEnvUtils(EnvUtils $new): void {
        WithUtilsCache::set('envUtils', $new);
    }
}
