<?php

namespace Olz\Apps\SearchEngines;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Suchmaschinen-Analyse';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/search_engines';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('all', $user);
    }
}
