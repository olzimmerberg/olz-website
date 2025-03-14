<?php

namespace Olz\Utils;

use Symfony\Contracts\Service\Attribute\Required;

trait AuthUtilsTrait {
    protected AuthUtils $authUtils;

    #[Required]
    public function setAuthUtils(AuthUtils $authUtils): void {
        $this->authUtils = $authUtils;
    }
}
