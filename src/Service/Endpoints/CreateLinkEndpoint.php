<?php

namespace Olz\Service\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Service\Link;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;

/**
 * @phpstan-import-type OlzLinkId from LinkEndpointTrait
 * @phpstan-import-type OlzLinkData from LinkEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzLinkId, OlzLinkData>
 */
class CreateLinkEndpoint extends OlzCreateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use LinkEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(LinkEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('links');

        $entity = new Link();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
