<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzKarteId from KarteEndpointTrait
 * @phpstan-import-type OlzKarteData from KarteEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzKarteKind from KarteEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzKarteId, OlzKarteData>
 */
class GetKarteEndpoint extends OlzGetEntityTypedEndpoint {
    use KarteEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(KarteEndpointTrait::class);
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
