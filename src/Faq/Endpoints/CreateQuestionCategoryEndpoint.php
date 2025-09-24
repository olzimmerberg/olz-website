<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Faq\QuestionCategory;

/**
 * @phpstan-import-type OlzQuestionCategoryId from QuestionCategoryEndpointTrait
 * @phpstan-import-type OlzQuestionCategoryData from QuestionCategoryEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzQuestionCategoryId, OlzQuestionCategoryData>
 */
class CreateQuestionCategoryEndpoint extends OlzCreateEntityTypedEndpoint {
    use QuestionCategoryEndpointTrait;

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = new QuestionCategory();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
