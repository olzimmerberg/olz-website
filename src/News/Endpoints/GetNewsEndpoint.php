<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetNewsEndpoint extends OlzGetEntityEndpoint {
    use NewsEndpointTrait;

    public static function getIdent(): string {
        return 'GetNewsEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
