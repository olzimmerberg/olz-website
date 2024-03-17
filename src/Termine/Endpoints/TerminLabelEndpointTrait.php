<?php

namespace Olz\Termine\Endpoints;

use Olz\Entity\Termine\TerminLabel;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait TerminLabelEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzTerminLabelDataOrNull' : 'OlzTerminLabelData',
            'field_structure' => [
                'ident' => new FieldTypes\StringField(['max_length' => 31]),
                'name' => new FieldTypes\StringField(['max_length' => 127]),
                'details' => new FieldTypes\StringField(['allow_empty' => true]),
                'icon' => new FieldTypes\StringField(['allow_null' => true]),
                'position' => new FieldTypes\IntegerField(['allow_null' => true]),
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

    public function getEntityData(TerminLabel $entity): array {
        return [
            'ident' => $entity->getIdent() ? $entity->getIdent() : '-',
            'name' => $entity->getName() ? $entity->getName() : '-',
            'details' => $entity->getDetails() ?? '',
            'icon' => $entity->getIcon() ? $entity->getIcon() : null,
            'position' => $entity->getPosition() ?? 0,
            'imageIds' => $entity->getStoredImageUploadIds(),
            'fileIds' => $entity->getStoredFileUploadIds(),
        ];
    }

    public function updateEntityWithData(TerminLabel $entity, array $input_data): void {
        $valid_icon_file_id = $this->uploadUtils()->getValidUploadId($input_data['icon']);

        $entity->setIdent($input_data['ident']);
        $entity->setName($input_data['name']);
        $entity->setDetails($input_data['details']);
        $entity->setIcon($valid_icon_file_id);
        $entity->setPosition($input_data['position']);
    }

    public function persistUploads(TerminLabel $entity, array $input_data): void {
        $this->persistOlzImages($entity, $input_data['imageIds']);
        $this->persistOlzFiles($entity, $input_data['fileIds']);
        if ($input_data['icon']) {
            $this->persistOlzFiles($entity, [$input_data['icon']]);
        }
    }

    public function editUploads(TerminLabel $entity): void {
        $image_ids = $this->uploadUtils()->getStoredUploadIds("{$entity->getImagesPathForStorage()}img/");
        $this->editOlzImages($entity, $image_ids);
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): TerminLabel {
        $repo = $this->entityManager()->getRepository(TerminLabel::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
