<?php

namespace Olz\Apps\Panini2024\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait Panini2024UtilsTrait {
    protected function paniniUtils(): Panini2024Utils {
        $util = WithUtilsCache::get('paniniUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setPaniniUtils(Panini2024Utils $new): void {
        WithUtilsCache::set('paniniUtils', $new);
    }
}
