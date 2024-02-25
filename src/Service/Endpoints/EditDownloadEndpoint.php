<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditDownloadEndpoint extends OlzEditEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'EditDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $download = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($download, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($download);

        return [
            'id' => $download->getId(),
            'meta' => $download->getMetaData(),
            'data' => $this->getEntityData($download),
        ];
    }
}
