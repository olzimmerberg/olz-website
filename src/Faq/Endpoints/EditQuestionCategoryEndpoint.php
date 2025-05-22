<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzQuestionCategoryId from QuestionCategoryEndpointTrait
 * @phpstan-import-type OlzQuestionCategoryData from QuestionCategoryEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzQuestionCategoryId, OlzQuestionCategoryData>
 */
class EditQuestionCategoryEndpoint extends OlzEditEntityTypedEndpoint {
    use EntityUtilsTrait;
    use QuestionCategoryEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(QuestionCategoryEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'faq')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        return [
            'id' => $entity->getId() ?? 0,
            'meta' => $entity->getMetaData(),
            'data' => $this->getEntityData($entity),
        ];
    }
}
