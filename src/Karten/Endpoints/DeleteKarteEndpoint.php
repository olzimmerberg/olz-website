<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteKarteEndpoint extends OlzDeleteEntityEndpoint {
    use KarteEndpointTrait;

    public static function getIdent(): string {
        return 'DeleteKarteEndpoint';
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'karten')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
