<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Link;

class CreateLinkEndpoint extends OlzCreateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent(): string {
        return 'CreateLinkEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('links');

        $entity = new Link();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
