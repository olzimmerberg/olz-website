<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\HttpError;

class UpdateLinkEndpoint extends OlzUpdateEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'UpdateLinkEndpoint';
    }

    protected function handle($input) {
        $has_access = $this->authUtils()->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $link_repo = $this->entityManager()->getRepository(Link::class);
        $link = $link_repo->findOneBy(['id' => $input['id']]);

        if (!$link) {
            throw new HttpError(404, "Nicht gefunden.");
        }
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
