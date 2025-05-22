<?php

namespace Olz\News\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait NewsFilterUtilsTrait {
    protected function newsFilterUtils(): NewsFilterUtils {
        $util = WithUtilsCache::get('newsFilterUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setNewsFilterUtils(NewsFilterUtils $new): void {
        WithUtilsCache::set('newsFilterUtils', $new);
    }
}
