<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait SessionTrait {
    protected function session(): Session {
        $util = WithUtilsCache::get('session');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSession(Session $new): void {
        WithUtilsCache::set('session', $new);
    }
}
