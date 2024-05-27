<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Download;

class CreateDownloadEndpoint extends OlzCreateEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent(): string {
        return 'CreateDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('downloads');

        $entity = new Download();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
