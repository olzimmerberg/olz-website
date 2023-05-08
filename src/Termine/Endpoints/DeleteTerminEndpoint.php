<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class DeleteTerminEndpoint extends OlzDeleteEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'DeleteTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $entity_id]);

        if (!$termin) {
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($termin, null)) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityManager()->remove($termin);
        $this->entityManager()->flush();
        return ['status' => 'OK'];
    }
}
