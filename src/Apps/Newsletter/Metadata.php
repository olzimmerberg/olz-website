<?php

namespace Olz\Apps\Newsletter;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Newsletter';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/newsletter';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('any', $user);
    }
}
