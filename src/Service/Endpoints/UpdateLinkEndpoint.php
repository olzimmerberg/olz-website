<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateLinkEndpoint extends OlzUpdateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'UpdateLinkEndpoint';
    }

    protected function handle($input) {
        $this->checkPermission('any');

        $link = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($link, $input['meta'], 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($link, $input['meta'] ?? []);
        $this->updateEntityWithData($link, $input['data']);

        $this->entityManager()->persist($link);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $link->getId(),
        ];
    }
}
