<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateTerminTemplateEndpoint extends OlzUpdateEntityEndpoint {
    use TerminTemplateEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateTerminTemplateEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
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
