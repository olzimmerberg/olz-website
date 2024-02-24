<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateTerminEndpoint extends OlzUpdateEntityEndpoint {
    use TerminEndpointTrait;

    public static function getIdent() {
        return 'UpdateTerminEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('termine');

        $termin = $this->getEntityById($input['id']);

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
