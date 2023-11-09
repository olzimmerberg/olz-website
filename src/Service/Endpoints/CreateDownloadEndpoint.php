<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Download;
use PhpTypeScriptApi\HttpError;

class CreateDownloadEndpoint extends OlzCreateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'CreateDownloadEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $download = new Download();
        $this->entityUtils()->createOlzEntity($download, $input['meta']);
        $this->updateEntityWithData($download, $input['data']);

        $this->entityManager()->persist($download);
        $this->entityManager()->flush();
        $this->persistUploads($download, $input['data']);

        return [
            'status' => 'OK',
            'id' => $download->getId(),
        ];
    }
}
