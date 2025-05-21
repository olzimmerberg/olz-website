<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait HttpUtilsTrait {
    protected function httpUtils(): HttpUtils {
        $util = WithUtilsCache::get('httpUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setHttpUtils(HttpUtils $new): void {
        WithUtilsCache::set('httpUtils', $new);
    }
}
