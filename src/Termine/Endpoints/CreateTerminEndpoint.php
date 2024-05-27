<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\Termin;

class CreateTerminEndpoint extends OlzCreateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent(): string {
        return 'CreateTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $entity = new Termin();
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
