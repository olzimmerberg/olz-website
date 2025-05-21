<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait GeneralUtilsTrait {
    protected function generalUtils(): GeneralUtils {
        $util = WithUtilsCache::get('generalUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setGeneralUtils(GeneralUtils $new): void {
        WithUtilsCache::set('generalUtils', $new);
    }
}
