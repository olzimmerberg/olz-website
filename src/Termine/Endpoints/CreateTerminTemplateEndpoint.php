<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Termine\TerminTemplate;

class CreateTerminTemplateEndpoint extends OlzCreateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent(): string {
        return 'CreateTerminTemplateEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminTemplate();
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
