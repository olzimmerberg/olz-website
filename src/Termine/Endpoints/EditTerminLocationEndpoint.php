<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminLocationId from TerminLocationEndpointTrait
 * @phpstan-import-type OlzTerminLocationData from TerminLocationEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminLocationId, OlzTerminLocationData>
 */
class EditTerminLocationEndpoint extends OlzEditEntityTypedEndpoint {
    use TerminLocationEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(TerminLocationEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'termine_admin')) {
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
