<?php

namespace Olz\Anniversary\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait AnniversaryUtilsTrait {
    protected function anniversaryUtils(): AnniversaryUtils {
        $util = WithUtilsCache::get('anniversaryUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setAnniversaryUtils(AnniversaryUtils $new): void {
        WithUtilsCache::set('anniversaryUtils', $new);
    }
}
