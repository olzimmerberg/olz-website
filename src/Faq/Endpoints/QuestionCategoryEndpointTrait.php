<?php

namespace Olz\Faq\Endpoints;

use Olz\Entity\Faq\QuestionCategory;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzQuestionCategoryId int
 * @phpstan-type OlzQuestionCategoryData array{
 *   position: int,
 *   name: non-empty-string,
 * }
 */
trait QuestionCategoryEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzQuestionCategoryData */
    public function getEntityData(QuestionCategory $entity): array {
        return [
            'position' => $entity->getPosition(),
            'name' => $entity->getName() ? $entity->getName() : '-',
        ];
    }

    /** @param OlzQuestionCategoryData $input_data */
    public function updateEntityWithData(QuestionCategory $entity, array $input_data): void {
        $entity->setPosition(intval($input_data['position']));
        $entity->setName($input_data['name']);
    }

    protected function getEntityById(int $id): QuestionCategory {
        $repo = $this->entityManager()->getRepository(QuestionCategory::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
