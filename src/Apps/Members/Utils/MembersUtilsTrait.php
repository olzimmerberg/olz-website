<?php

namespace Olz\Apps\Members\Utils;

use Olz\Utils\WithUtilsCache;
use Symfony\Contracts\Service\Attribute\Required;

trait MembersUtilsTrait {
    protected function membersUtils(): MembersUtils {
        $util = WithUtilsCache::get('membersUtils');
        assert($util);
        return $util;
    }

    #[Required]
    public function setMembersUtils(MembersUtils $new): void {
        WithUtilsCache::set('membersUtils', $new);
    }
}
