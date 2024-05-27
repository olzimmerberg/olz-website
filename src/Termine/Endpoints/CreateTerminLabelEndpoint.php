<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminLabel;

class CreateTerminLabelEndpoint extends OlzCreateEntityEndpoint {
    use TerminLabelEndpointTrait;

    public static function getIdent(): string {
        return 'CreateTerminLabelEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $entity = new TerminLabel();
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
