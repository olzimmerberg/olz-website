<?php

namespace Olz\Apps\Monitoring;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Monitoring';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/monitoring';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('all', $user);
    }
}
