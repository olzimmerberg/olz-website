<?php

namespace Olz\Captcha\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait CaptchaUtilsTrait {
    protected function captchaUtils(): CaptchaUtils {
        $util = WithUtilsCache::get('captchaUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setCaptchaUtils(CaptchaUtils $new): void {
        WithUtilsCache::set('captchaUtils', $new);
    }
}
