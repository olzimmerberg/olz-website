<?php

namespace Olz\Utils;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LogTrait {
    use LoggerAwareTrait;

    protected function log(): LoggerInterface {
        $util = WithUtilsCache::get('log');
        assert($util);
        return $util;
    }

    #[Required]
    public function setLog(LoggerInterface $new): void {
        $this->logger = $new;
        WithUtilsCache::set('log', $new);
    }
}
