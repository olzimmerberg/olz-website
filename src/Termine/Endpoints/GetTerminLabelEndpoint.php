<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminLabelId from TerminLabelEndpointTrait
 * @phpstan-import-type OlzTerminLabelData from TerminLabelEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminLabelId, OlzTerminLabelData>
 */
class GetTerminLabelEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminLabelEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(TerminLabelEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId(),
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
