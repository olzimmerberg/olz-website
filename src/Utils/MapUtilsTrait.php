<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait MapUtilsTrait {
    protected function mapUtils(): MapUtils {
        $util = WithUtilsCache::get('mapUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setMapUtils(MapUtils $new): void {
        WithUtilsCache::set('mapUtils', $new);
    }
}
