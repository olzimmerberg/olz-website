<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateTerminLocationEndpoint extends OlzUpdateEntityEndpoint {
    use TerminLocationEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminLocationEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin_location = $this->getEntityById($input['id']);

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
