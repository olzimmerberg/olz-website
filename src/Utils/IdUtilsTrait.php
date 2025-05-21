<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait IdUtilsTrait {
    protected function idUtils(): IdUtils {
        $util = WithUtilsCache::get('idUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setIdUtils(IdUtils $new): void {
        WithUtilsCache::set('idUtils', $new);
    }
}
