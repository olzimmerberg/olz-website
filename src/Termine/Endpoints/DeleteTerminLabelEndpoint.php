<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteTerminLabelEndpoint extends OlzDeleteEntityEndpoint {
    use TerminLabelEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminLabelEndpoint';
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
