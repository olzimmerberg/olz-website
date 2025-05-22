<?php

namespace Olz\Termine\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait TermineFilterUtilsTrait {
    protected function termineFilterUtils(): TermineFilterUtils {
        $util = WithUtilsCache::get('termineFilterUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setTermineFilterUtils(TermineFilterUtils $new): void {
        WithUtilsCache::set('termineFilterUtils', $new);
    }
}
