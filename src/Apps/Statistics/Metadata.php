<?php

namespace Olz\Apps\Statistics;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Website-Statistiken';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/statistics';
    }

    public function isAccessibleToUser(?User $user): bool {
        return $this->authUtils()->hasPermission('vorstand', $user);
    }
}
