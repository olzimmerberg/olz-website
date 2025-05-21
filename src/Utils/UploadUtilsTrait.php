<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait UploadUtilsTrait {
    protected function uploadUtils(): UploadUtils {
        $util = WithUtilsCache::get('uploadUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setUploadUtils(UploadUtils $new): void {
        WithUtilsCache::set('uploadUtils', $new);
    }
}
