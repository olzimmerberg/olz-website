<?php

namespace Olz\News\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzNewsId from NewsEndpointTrait
 * @phpstan-import-type OlzNewsData from NewsEndpointTrait
 *
 * TODO: Those should not be necessary!
 * @phpstan-import-type OlzNewsFormat from NewsEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzNewsId, OlzNewsData>
 */
class EditNewsEndpoint extends OlzEditEntityTypedEndpoint {
    use EntityUtilsTrait;
    use NewsEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureNewsEndpointTrait();
        $this->phpStanUtils->registerTypeImport(NewsEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('any');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'news')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->editUploads($entity);

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
