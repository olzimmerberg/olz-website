<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Termine\TerminLocation;
use PhpTypeScriptApi\HttpError;

class UpdateTerminLocationEndpoint extends OlzUpdateEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminLocationEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('termine');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $termin_location_repo = $this->entityManager()->getRepository(TerminLocation::class);
        $termin_location = $termin_location_repo->findOneBy(['id' => $input['id']]);

        if (!$termin_location) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($termin_location, $input['meta'], 'termine')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($termin_location, $input['meta'] ?? []);
        $this->updateEntityWithData($termin_location, $input['data']);

        $this->entityManager()->persist($termin_location);
        $this->entityManager()->flush();
        $this->persistUploads($termin_location);

        return [
            'status' => 'OK',
            'id' => $termin_location->getId(),
        ];
    }
}
