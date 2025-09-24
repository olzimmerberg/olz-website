<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzSnippetId from SnippetEndpointTrait
 * @phpstan-import-type OlzSnippetData from SnippetEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzSnippetId, OlzSnippetData>
 */
class EditSnippetEndpoint extends OlzEditEntityTypedEndpoint {
    use SnippetEndpointTrait;

    protected function handle(mixed $input): mixed {
        $id = $input['id'];
        $this->checkPermission("snippet_{$id}");

        $entity = $this->getEntityById($id);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, "snippet_{$id}")) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
