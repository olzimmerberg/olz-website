<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzQuestionCategoryId from QuestionCategoryEndpointTrait
 * @phpstan-import-type OlzQuestionCategoryData from QuestionCategoryEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzQuestionCategoryId, OlzQuestionCategoryData>
 */
class UpdateQuestionCategoryEndpoint extends OlzUpdateEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use QuestionCategoryEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(QuestionCategoryEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, $input['meta'], 'faq')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [
            'id' => $entity->getId() ?? 0,
        ];
    }
}
