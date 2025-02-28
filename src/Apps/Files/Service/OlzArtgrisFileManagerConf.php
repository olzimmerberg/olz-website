<?php

namespace Olz\Apps\Files\Service;

use Artgris\Bundle\FileManagerBundle\Service\CustomConfServiceInterface;
use Olz\Utils\WithUtilsTrait;

class OlzArtgrisFileManagerConf implements CustomConfServiceInterface {
    use WithUtilsTrait;

    /** @return array{dir: string} */
    // @phpstan-ignore-next-line
    public function getConf($extra = []): array {
        $data_path = $this->envUtils()->getDataPath();

        $user = $this->authUtils()->getCurrentUser();
        if (!$user) {
            $this->httpUtils()->dieWithHttpError(401);
            throw new \Exception('should already have failed');
        }
        if (!$this->authUtils()->hasPermission('ftp', $user)) {
            $this->httpUtils()->dieWithHttpError(403);
            throw new \Exception('should already have failed');
        }
        $user_root = $user->getRoot();
        if (!$user_root) {
            $this->httpUtils()->dieWithHttpError(403);
            throw new \Exception('should already have failed');
        }

        return [
            'dir' => "{$data_path}OLZimmerbergAblage/{$user_root}",
        ];
    }
}
