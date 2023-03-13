<?php

namespace Olz\Apps\Panini2024;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Panini \'24';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/panini24/';
    }

    public function isAccessibleToUser(?User $user): bool {
        $auth_utils = AuthUtils::fromEnv();
        return $auth_utils->hasPermission('panini2024', $user);
    }
}
