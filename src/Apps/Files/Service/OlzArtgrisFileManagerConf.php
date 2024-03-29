<?php

namespace Olz\Apps\Files\Service;

use Artgris\Bundle\FileManagerBundle\Service\CustomConfServiceInterface;
use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;

class OlzArtgrisFileManagerConf implements CustomConfServiceInterface {
    public function getConf($extra = []) {
        $env_utils = EnvUtils::fromEnv();
        $data_path = $env_utils->getDataPath();

        $auth_utils = AuthUtils::fromEnv();
        $user = $auth_utils->getCurrentUser();
        if (!$user) {
            HttpUtils::fromEnv()->dieWithHttpError(401);
        }
        if (!$auth_utils->hasPermission('ftp', $user)) {
            HttpUtils::fromEnv()->dieWithHttpError(403);
        }
        $user_root = $user ? $user->getRoot() : '';
        if (!$user_root) {
            HttpUtils::fromEnv()->dieWithHttpError(403);
        }

        return [
            'dir' => "{$data_path}OLZimmerbergAblage/{$user_root}",
        ];
    }
}
