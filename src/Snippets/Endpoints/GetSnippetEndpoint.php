<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;

class GetSnippetEndpoint extends OlzGetEntityEndpoint {
    use SnippetEndpointTrait;

    public static function getIdent(): string {
        return 'GetSnippetEndpoint';
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
