<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait StravaUtilsTrait {
    protected function stravaUtils(): StravaUtils {
        $util = WithUtilsCache::get('stravaUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setStravaUtils(StravaUtils $new): void {
        WithUtilsCache::set('stravaUtils', $new);
    }
}
