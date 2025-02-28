<?php

namespace Olz\Snippets\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzSnippetId from SnippetEndpointTrait
 * @phpstan-import-type OlzSnippetData from SnippetEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzSnippetId, OlzSnippetData>
 */
class UpdateSnippetEndpoint extends OlzUpdateEntityTypedEndpoint {
    use SnippetEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(SnippetEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $id = $input['id'];
        $this->checkPermission("snippet_{$id}");

        $entity = $this->getEntityById($id);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], "snippet_{$id}")) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
