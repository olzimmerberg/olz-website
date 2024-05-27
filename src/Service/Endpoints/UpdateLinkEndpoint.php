<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateLinkEndpoint extends OlzUpdateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateLinkEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
