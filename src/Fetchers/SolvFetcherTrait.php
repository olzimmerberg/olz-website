<?php

namespace Olz\Fetchers;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait SolvFetcherTrait {
    protected function solvFetcher(): SolvFetcher {
        $util = WithUtilsCache::get('solvFetcher');
        assert($util);
        return $util;
    }

    #[Required]
    public function setSolvFetcher(SolvFetcher $new): void {
        WithUtilsCache::set('solvFetcher', $new);
    }
}
