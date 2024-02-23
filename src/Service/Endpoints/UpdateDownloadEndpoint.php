<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateDownloadEndpoint extends OlzUpdateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'UpdateDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $download = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($download, $input['meta'], 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($download, $input['meta'] ?? []);
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
