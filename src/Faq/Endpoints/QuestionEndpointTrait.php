<?php

namespace Olz\Faq\Endpoints;

use Olz\Entity\Faq\Question;
use Olz\Entity\Faq\QuestionCategory;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzQuestionId int
 * @phpstan-type OlzQuestionData array{
 *   ident: non-empty-string,
 *   question: non-empty-string,
 *   categoryId?: ?int,
 *   positionWithinCategory?: ?int,
 *   answer: non-empty-string,
 *   imageIds: array<non-empty-string>,
 *   fileIds: array<non-empty-string>,
 * }
 */
trait QuestionEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzQuestionData */
    public function getEntityData(Question $entity): array {
        return [
            'ident' => $entity->getIdent() ? $entity->getIdent() : '-',
            'question' => $entity->getQuestion() ? $entity->getQuestion() : '-',
            'categoryId' => $entity->getCategory()?->getId(),
            'positionWithinCategory' => $entity->getPositionWithinCategory(),
            'answer' => $entity->getAnswer() ? $entity->getAnswer() : '-',
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
        ];
    }

    /** @param OlzQuestionData $input_data */
    public function updateEntityWithData(Question $entity, array $input_data): void {
        $category_repo = $this->entityManager()->getRepository(QuestionCategory::class);
        $category_id = $input_data['categoryId'] ?? null;
        $category = $category_repo->findOneBy(['id' => $category_id]);

        $entity->setIdent($input_data['ident']);
        $entity->setQuestion($input_data['question']);
        $entity->setCategory($category);
        $entity->setPositionWithinCategory($input_data['positionWithinCategory'] ?? 0);
        $entity->setAnswer($input_data['answer']);
    }

    /** @param OlzQuestionData $input_data */
    public function persistUploads(Question $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(Question $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Question {
        $repo = $this->entityManager()->getRepository(Question::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
