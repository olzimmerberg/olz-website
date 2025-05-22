<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class CreateTerminTemplateEndpoint extends OlzCreateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use TerminTemplateEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureTerminTemplateEndpointTrait();
        $this->phpStanUtils->registerTypeImport(TerminTemplateEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('termine');

        $entity = new TerminTemplate();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
