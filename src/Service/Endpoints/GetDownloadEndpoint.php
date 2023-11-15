<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Service\Download;
use PhpTypeScriptApi\HttpError;

class GetDownloadEndpoint extends OlzGetEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'GetDownloadEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $download_repo = $this->entityManager()->getRepository(Download::class);
        $download = $download_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $download->getId(),
            'meta' => $download->getMetaData(),
            'data' => $this->getEntityData($download),
        ];
    }
}
