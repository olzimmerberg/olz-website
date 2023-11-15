<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Service\Download;
use PhpTypeScriptApi\HttpError;

class UpdateDownloadEndpoint extends OlzUpdateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'UpdateDownloadEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $download_repo = $this->entityManager()->getRepository(Download::class);
        $download = $download_repo->findOneBy(['id' => $input['id']]);

        if (!$download) {
            throw new HttpError(404, "Nicht gefunden.");
        }
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
