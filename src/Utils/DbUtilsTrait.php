<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait DbUtilsTrait {
    protected function dbUtils(): DbUtils {
        $util = WithUtilsCache::get('dbUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setDbUtils(DbUtils $new): void {
        WithUtilsCache::set('dbUtils', $new);
    }
}
