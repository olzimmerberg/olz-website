<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminLocation;

class CreateTerminLocationEndpoint extends OlzCreateEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent(): string {
        return 'CreateTerminLocationEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminLocation();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
