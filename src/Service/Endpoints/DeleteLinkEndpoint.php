<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class DeleteLinkEndpoint extends OlzDeleteEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'DeleteLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $link = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($link, null, 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $link->setOnOff(0);
        $this->entityManager()->persist($link);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
