<?php

namespace Olz\Apps\Results;

use Olz\Apps\BaseAppMetadata;
use Olz\Entity\User;

class Metadata extends BaseAppMetadata {
    public function getDisplayName(): string {
        return 'Resultate';
    }

    public function getPath(): string {
        return __DIR__;
    }

    public function getHref(): string {
        return 'apps/resultate/';
    }

    public function isAccessibleToUser(?User $user): bool {
        return true;
    }
}
