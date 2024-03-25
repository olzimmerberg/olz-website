<?php

namespace Olz\Snippets\Endpoints;

use Olz\Entity\Snippets\Snippet;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait SnippetEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzSnippetDataOrNull' : 'OlzSnippetData',
            'field_structure' => [
                'text' => new FieldTypes\StringField(['allow_empty' => true]),
                'imageIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
                'fileIds' => new FieldTypes\ArrayField([
                    'item_field' => new FieldTypes\StringField([]),
                ]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(Snippet $entity): array {
        return [
            'text' => $entity->getText() ?? '',
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
        ];
    }

    public function updateEntityWithData(Snippet $entity, array $input_data): void {
        $entity->setText($input_data['text']);
    }

    public function persistUploads(Snippet $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
    }

    public function editUploads(Snippet $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Snippet {
        $repo = $this->entityManager()->getRepository(Snippet::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            $has_access = $this->authUtils()->hasPermission("snippet_{$id}");
            if (!$has_access) {
                throw new HttpError(404, "Nicht gefunden.");
            }
            $entity = new Snippet();
            $this->entityUtils()->createOlzEntity($entity, ['onOff' => true]);
            $entity->setId($id);
            $entity->setText('');
            $this->entityManager()->persist($entity);
            $this->entityManager()->flush();
        }
        return $entity;
    }
}
