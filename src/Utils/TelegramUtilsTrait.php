<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait TelegramUtilsTrait {
    protected function telegramUtils(): TelegramUtils {
        $util = WithUtilsCache::get('telegramUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setTelegramUtils(TelegramUtils $new): void {
        WithUtilsCache::set('telegramUtils', $new);
    }
}
