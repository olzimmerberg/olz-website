<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\Termin;

/**
 * @phpstan-import-type OlzTerminId from TerminEndpointTrait
 * @phpstan-import-type OlzTerminData from TerminEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminId, OlzTerminData>
 */
class CreateTerminEndpoint extends OlzCreateEntityTypedEndpoint {
    use TerminEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureTerminEndpointTrait();
        $this->phpStanUtils->registerTypeImport(TerminEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new Termin();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId(),
        ];
    }
}
