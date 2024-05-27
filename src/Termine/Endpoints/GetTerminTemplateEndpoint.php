<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetTerminTemplateEndpoint extends OlzGetEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent(): string {
        return 'GetTerminTemplateEndpoint';
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
