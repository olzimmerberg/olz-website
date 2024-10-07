<?php

namespace Olz\Apps\Import;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Import';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/import';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('termine', $user);
    }
}
