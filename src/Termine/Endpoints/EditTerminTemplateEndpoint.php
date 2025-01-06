<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class EditTerminTemplateEndpoint extends OlzEditEntityTypedEndpoint {
    use TerminTemplateEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureTerminTemplateEndpointTrait();
        $this->phpStanUtils->registerTypeImport(TerminTemplateEndpointTrait::class);
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
