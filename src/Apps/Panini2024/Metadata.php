<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\Users\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Panini \'24';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/panini24';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('any', $user);
    }
}
