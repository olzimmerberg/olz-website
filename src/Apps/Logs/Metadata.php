<?php

namespace Olz\Apps\Logs;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Logs';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/logs';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('all', $user);
    }
}
