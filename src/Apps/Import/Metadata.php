<?php

namespace Olz\Apps\Import;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;
use Olz\Utils\AuthUtils;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Import';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/import/';
    }

    public function isAccessibleToUser(?User $user): bool {
        $auth_utils = AuthUtils::fromEnv();
        return $auth_utils->hasPermission('termine', $user);
    }
}
