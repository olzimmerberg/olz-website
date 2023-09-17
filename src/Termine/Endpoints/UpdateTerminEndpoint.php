<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Termine\Termin;
use PhpTypeScriptApi\HttpError;

class UpdateTerminEndpoint extends OlzUpdateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_repo = $this->entityManager()->getRepository(Termin::class);
        $termin = $termin_repo->findOneBy(['id' => $input['id']]);

        if (!$termin) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($termin, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($termin, $input['meta'] ?? []);
        $this->updateEntityWithData($termin, $input['data']);

        $this->entityManager()->persist($termin);
        $this->entityManager()->flush();
        $this->persistUploads($termin, $input['data']);

        return [
            'status' => 'OK',
            'id' => $termin->getId(),
        ];
    }
}
