<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzQuestionCategoryId from QuestionCategoryEndpointTrait
 * @phpstan-import-type OlzQuestionCategoryData from QuestionCategoryEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzQuestionCategoryId, OlzQuestionCategoryData>
 */
class GetQuestionCategoryEndpoint extends OlzGetEntityTypedEndpoint {
    use QuestionCategoryEndpointTrait;

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
