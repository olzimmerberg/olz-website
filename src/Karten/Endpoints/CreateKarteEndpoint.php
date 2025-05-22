<?php

namespace Olz\Karten\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Karten\Karte;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;

/**
 * @phpstan-import-type OlzKarteId from KarteEndpointTrait
 * @phpstan-import-type OlzKarteData from KarteEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzKarteKind from KarteEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzKarteId, OlzKarteData>
 */
class CreateKarteEndpoint extends OlzCreateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use KarteEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(KarteEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('karten');

        $entity = new Karte();
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
