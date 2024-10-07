<?php

namespace Olz\Apps\Files;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Dateien';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/files';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('ftp', $user);
    }
}
