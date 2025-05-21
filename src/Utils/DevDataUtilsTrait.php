<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait DevDataUtilsTrait {
    protected function devDataUtils(): DevDataUtils {
        $util = WithUtilsCache::get('devDataUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDevDataUtils(DevDataUtils $new): void {
        WithUtilsCache::set('devDataUtils', $new);
    }
}
