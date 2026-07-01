<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class EditTerminTemplateEndpoint extends OlzEditEntityTypedEndpoint {
    use TerminTemplateEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        // We intentionally don't do a permission check here.
        // The OlzEditTerminModal needs to call this to stage the images and files.

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
