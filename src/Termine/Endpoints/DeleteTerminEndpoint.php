<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteTerminEndpoint extends OlzDeleteEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity->setOnOff(0);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
