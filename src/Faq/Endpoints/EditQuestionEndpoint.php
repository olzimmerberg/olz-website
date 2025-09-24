<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzEditEntityTypedEndpoint;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzQuestionId from QuestionEndpointTrait
 * @phpstan-import-type OlzQuestionData from QuestionEndpointTrait
 *
 * @extends OlzEditEntityTypedEndpoint<OlzQuestionId, OlzQuestionData>
 */
class EditQuestionEndpoint extends OlzEditEntityTypedEndpoint {
    use QuestionEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'faq')) {
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
