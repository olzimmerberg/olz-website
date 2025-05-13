<?php

namespace Olz\Apps\Members;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Mitglieder';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/mitglieder';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('vorstand', $user);
    }
}
