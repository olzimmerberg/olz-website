<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait SymfonyUtilsTrait {
    protected function symfonyUtils(): SymfonyUtils {
        $util = WithUtilsCache::get('symfonyUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSymfonyUtils(SymfonyUtils $new): void {
        WithUtilsCache::set('symfonyUtils', $new);
    }
}
