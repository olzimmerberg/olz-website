<?php

namespace Olz\Apps\Files\Service;

use Artgris\Bundle\FileManagerBundle\Service\CustomConfServiceInterface;
use Olz\Utils\WithUtilsTrait;

class OlzArtgrisFileManagerConf implements CustomConfServiceInterface {
    use WithUtilsTrait;

    public function getConf($extra = []) {
        $data_path = $this->envUtils()->getDataPath();

        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            $this->httpUtils()->dieWithHttpError(401);
        }
        if (!$this->authUtils()->hasPermission('ftp', $user)) {
            $this->httpUtils()->dieWithHttpError(403);
        }
        $user_root = $user ? $user->getRoot() : '';
        if (!$user_root) {
            $this->httpUtils()->dieWithHttpError(403);
        }

        return [
            'dir' => "{$data_path}OLZimmerbergAblage/{$user_root}",
        ];
    }
}
