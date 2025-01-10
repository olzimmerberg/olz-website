<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzLinkId from LinkEndpointTrait
 * @phpstan-import-type OlzLinkData from LinkEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzLinkId, OlzLinkData>
 */
class GetLinkEndpoint extends OlzGetEntityTypedEndpoint {
    use LinkEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(LinkEndpointTrait::class);
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
