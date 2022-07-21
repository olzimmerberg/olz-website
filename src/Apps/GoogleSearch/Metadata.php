<?php

namespace Olz\Apps\GoogleSearch;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Suchmaschinen-Analyse';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return '/apps/search_engines/';
    }

    public function isAccessibleToUser(?User $user): bool {
        $auth_utils = AuthUtils::fromEnv();
        return $auth_utils->hasPermission('all', $user);
    }
}
