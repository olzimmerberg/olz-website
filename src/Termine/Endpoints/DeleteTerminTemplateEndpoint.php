<?php

namespace Olz\Termine\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzTerminTemplateId from TerminTemplateEndpointTrait
 * @phpstan-import-type OlzTerminTemplateData from TerminTemplateEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzTerminTemplateId, OlzTerminTemplateData>
 */
class DeleteTerminTemplateEndpoint extends OlzDeleteEntityTypedEndpoint {
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

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'termine_admin')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
