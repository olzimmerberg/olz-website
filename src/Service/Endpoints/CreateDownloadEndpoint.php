<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Download;

class CreateDownloadEndpoint extends OlzCreateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent() {
        return 'CreateDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('downloads');

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
