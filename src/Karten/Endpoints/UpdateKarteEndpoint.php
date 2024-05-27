<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateKarteEndpoint extends OlzUpdateEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent(): string {
        return 'UpdateKarteEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'karten')) {
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
