<?php

namespace Olz\Apps\Oev\Utils;

use Olz\Common\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait CoordinateUtilsTrait {
    protected function coordinateUtils(): CoordinateUtils {
        $util = WithUtilsCache::get('coordinateUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setCoordinateUtils(CoordinateUtils $new): void {
        WithUtilsCache::set('coordinateUtils', $new);
    }
}
