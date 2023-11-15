<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzDeleteEntityEndpoint;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\HttpError;

class DeleteLinkEndpoint extends OlzDeleteEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'DeleteLinkEndpoint';
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
            return ['status' => 'ERROR'];
        }

        if (!$this->entityUtils()->canUpdateOlzEntity($link, null, 'links')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $link->setOnOff(0);
        $this->entityManager()->persist($link);
        $this->entityManager()->flush();

        return ['status' => 'OK'];
    }
}
