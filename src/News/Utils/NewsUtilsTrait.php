<?php

namespace Olz\News\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait NewsUtilsTrait {
    protected function newsUtils(): NewsUtils {
        $util = WithUtilsCache::get('newsUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setNewsUtils(NewsUtils $new): void {
        WithUtilsCache::set('newsUtils', $new);
    }
}
