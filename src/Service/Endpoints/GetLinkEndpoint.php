<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityEndpoint;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\HttpError;

class GetLinkEndpoint extends OlzGetEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'GetLinkEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $link_repo = $this->entityManager()->getRepository(Link::class);
        $link = $link_repo->findOneBy(['id' => $input['id']]);

        return [
            'id' => $link->getId(),
            'meta' => $link->getMetaData(),
            'data' => $this->getEntityData($link),
        ];
    }
}
