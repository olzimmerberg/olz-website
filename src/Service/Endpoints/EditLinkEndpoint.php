<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzEntityEndpoint;
use Olz\Entity\Common\OlzEntity;
use Olz\Entity\Service\Link;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

class EditLinkEndpoint extends OlzEntityEndpoint {
    use LinkEndpointTrait;

    public static function getIdent() {
        return 'EditLinkEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
            'meta' => OlzEntity::getMetaField(/* allow_null= */ false),
            'data' => $this->getEntityDataField(/* allow_null= */ false),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => $this->getIdField(/* allow_null= */ false),
        ]]);
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
