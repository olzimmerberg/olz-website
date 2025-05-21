<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait EntityUtilsTrait {
    protected function entityUtils(): EntityUtils {
        $util = WithUtilsCache::get('entityUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEntityUtils(EntityUtils $new): void {
        WithUtilsCache::set('entityUtils', $new);
    }
}
