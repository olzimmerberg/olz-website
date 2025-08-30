<?php

namespace Olz\Suche\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait SearchUtilsTrait {
    protected function searchUtils(): SearchUtils {
        $util = WithUtilsCache::get('searchUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSearchUtils(SearchUtils $new): void {
        WithUtilsCache::set('searchUtils', $new);
    }
}
