<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\HttpError;

class EditLinkEndpoint extends OlzEditEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'EditLinkEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $entity_id = $input['id'];
        $link_repo = $this->entityManager()->getRepository(Link::class);
        $link = $link_repo->findOneBy(['id' => $entity_id]);

        if (!$link) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        if (!$this->entityUtils()->canUpdateOlzEntity($link, null, 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $link->getId(),
            'meta' => $link->getMetaData(),
            'data' => $this->getEntityData($link),
        ];
    }
}
