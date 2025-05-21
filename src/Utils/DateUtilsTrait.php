<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait DateUtilsTrait {
    protected function dateUtils(): DateUtils {
        $util = WithUtilsCache::get('dateUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDateUtils(DateUtils $new): void {
        WithUtilsCache::set('dateUtils', $new);
    }
}
