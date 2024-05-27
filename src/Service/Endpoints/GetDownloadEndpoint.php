<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetDownloadEndpoint extends OlzGetEntityEndpoint {
    use DownloadEndpointTrait;

    public static function getIdent(): string {
        return 'GetDownloadEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
