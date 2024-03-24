<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzEditEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class EditSnippetEndpoint extends OlzEditEntityEndpoint {
    use SnippetEndpointTrait;

    public static function getIdent() {
        return 'EditSnippetEndpoint';
    }

    protected function handle($input) {
        $id = $input['id'];
        $this->checkPermission("olz_text_{$id}");

        $entity = $this->getEntityById($id);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, "olz_text_{$id}")) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
