<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\HttpError;

class CreateLinkEndpoint extends OlzCreateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'CreateLinkEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $link = new Link();
        $this->entityUtils()->createOlzEntity($link, $input['meta']);
        $this->updateEntityWithData($link, $input['data']);

        $this->entityManager()->persist($link);
        $this->entityManager()->flush();

        return [
            'status' => 'OK',
            'id' => $link->getId(),
        ];
    }
}
