<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteDownloadEndpoint extends OlzDeleteEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'DeleteDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $download = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($download, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $download->setOnOff(0);
        $this->entityManager()->persist($download);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
