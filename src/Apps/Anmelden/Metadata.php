<?php

namespace Olz\Apps\Anmelden;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Anmelden';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/anmelden';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('all', $user);
    }
}
