<?php

namespace Olz\Apps\Files\Service;

use Artgris\Bundle\FileManagerBundle\Service\CustomConfServiceInterface;
use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;

class OlzArtgrisFileManagerConf implements CustomConfServiceInterface {
    public function getConf($extra = []) {
        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();

        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getAuthenticatedUser();
        $user_root = $user ? $user->getRoot() : '';

        return [
            'dir' => "{$data_path}OLZimmerbergAblage/{$user_root}",
        ];
    }
}
