<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzGetEntityTypedEndpoint;

/**
 * @phpstan-import-type OlzQuestionId from QuestionEndpointTrait
 * @phpstan-import-type OlzQuestionData from QuestionEndpointTrait
 *
 * @extends OlzGetEntityTypedEndpoint<OlzQuestionId, OlzQuestionData>
 */
class GetQuestionEndpoint extends OlzGetEntityTypedEndpoint {
    use QuestionEndpointTrait;

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
