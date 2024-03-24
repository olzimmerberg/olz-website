<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzUpdateEntityEndpoint;
use PhpTypeScriptApi\HttpError;

class UpdateSnippetEndpoint extends OlzUpdateEntityEndpoint {
    use SnippetEndpointTrait;

    public static function getIdent() {
        return 'UpdateSnippetEndpoint';
    }

    protected function handle($input) {
        $id = $input['id'];
        $this->checkPermission("olz_text_{$id}");

        $entity = $this->getEntityById($id);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], "olz_text_{$id}")) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta'] ?? []);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'status' => 'OK',
            'id' => $entity->getId(),
        ];
    }
}
