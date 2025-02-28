<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\TerminLocation;

/**
 * @phpstan-import-type OlzTerminLocationId from TerminLocationEndpointTrait
 * @phpstan-import-type OlzTerminLocationData from TerminLocationEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminLocationId, OlzTerminLocationData>
 */
class CreateTerminLocationEndpoint extends OlzCreateEntityTypedEndpoint {
    use TerminLocationEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(TerminLocationEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminLocation();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
