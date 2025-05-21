<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait EmailUtilsTrait {
    protected function emailUtils(): EmailUtils {
        $util = WithUtilsCache::get('emailUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setEmailUtils(EmailUtils $new): void {
        WithUtilsCache::set('emailUtils', $new);
    }
}
