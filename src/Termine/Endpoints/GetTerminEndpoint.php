<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class GetTerminEndpoint extends OlzGetEntityTypedEndpoint {
    use TerminEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureTerminEndpointTrait();
        $this->phpStanUtils->registerTypeImport(TerminEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
