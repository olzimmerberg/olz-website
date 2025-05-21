<?php

namespace Olz\Termine\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait TermineUtilsTrait {
    protected function termineUtils(): TermineUtils {
        $util = WithUtilsCache::get('termineUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setTermineUtils(TermineUtils $new): void {
        WithUtilsCache::set('termineUtils', $new);
    }
}
