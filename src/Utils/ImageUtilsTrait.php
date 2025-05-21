<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait ImageUtilsTrait {
    protected function imageUtils(): ImageUtils {
        $util = WithUtilsCache::get('imageUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setImageUtils(ImageUtils $new): void {
        WithUtilsCache::set('imageUtils', $new);
    }
}
