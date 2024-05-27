<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetTerminLabelEndpoint extends OlzGetEntityEndpoint {
    use TerminLabelEndpointTrait;

    public static function getIdent(): string {
        return 'GetTerminLabelEndpoint';
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
