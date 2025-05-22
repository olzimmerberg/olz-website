<?php

namespace Olz\Faq\Endpoints;

use Olz\Api\OlzDeleteEntityTypedEndpoint;
use Olz\Utils\EntityManagerTrait;
use Olz\Utils\EntityUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzQuestionId from QuestionEndpointTrait
 * @phpstan-import-type OlzQuestionData from QuestionEndpointTrait
 *
 * @extends OlzDeleteEntityTypedEndpoint<OlzQuestionId, OlzQuestionData>
 */
class DeleteQuestionEndpoint extends OlzDeleteEntityTypedEndpoint {
    use EntityManagerTrait;
    use EntityUtilsTrait;
    use QuestionEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->phpStanUtils->registerTypeImport(QuestionEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $this->checkPermission('faq');

        $entity = $this->getEntityById($input['id']);

        if (!$this->entityUtils()->canUpdateOlzEntity($entity, null, 'faq')) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $this->entityUtils()->updateOlzEntity($entity, ['onOff' => false]);
        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        return [];
    }
}
