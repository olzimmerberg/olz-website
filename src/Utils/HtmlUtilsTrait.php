<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait HtmlUtilsTrait {
    protected function htmlUtils(): HtmlUtils {
        $util = WithUtilsCache::get('htmlUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setHtmlUtils(HtmlUtils $new): void {
        WithUtilsCache::set('htmlUtils', $new);
    }
}
