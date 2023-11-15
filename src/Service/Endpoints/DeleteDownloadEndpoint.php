<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Service\Download;
use PhpTypeScriptApi\HttpError;

class DeleteDownloadEndpoint extends OlzDeleteEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'DeleteDownloadEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $download_repo = $this->entityManager()->getRepository(Download::class);
        $download = $download_repo->findOneBy(['id' => $entity_id]);

        if (!$download) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($download, null, 'downloads')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $download->setOnOff(0);
        $this->entityManager()->persist($download);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
