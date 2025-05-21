<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait AuthUtilsTrait {
    protected function authUtils(): AuthUtils {
        $util = WithUtilsCache::get('authUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setAuthUtils(AuthUtils $new): void {
        WithUtilsCache::set('authUtils', $new);
    }
}
