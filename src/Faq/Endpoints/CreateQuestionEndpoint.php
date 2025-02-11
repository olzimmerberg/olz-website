<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Faq\Question;

/**
 * @phpstan-import-type OlzQuestionId from QuestionEndpointTrait
 * @phpstan-import-type OlzQuestionData from QuestionEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzQuestionId, OlzQuestionData>
 */
class CreateQuestionEndpoint extends OlzCreateEntityTypedEndpoint {
    use QuestionEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(QuestionEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = new Question();
        $this->entityUtils()->createOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        return [
            'id' => $entity->getId(),
        ];
    }
}
