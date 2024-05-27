<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetKarteEndpoint extends OlzGetEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent(): string {
        return 'GetKarteEndpoint';
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
