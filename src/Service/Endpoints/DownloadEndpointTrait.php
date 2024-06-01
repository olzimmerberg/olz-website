<?php

namespace Olz\Service\Endpoints;

use Olz\Entity\Service\Download;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait DownloadEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzDownloadDataOrNull' : 'OlzDownloadData',
            'field_structure' => [
                'name' => new FieldTypes\StringField([]),
                'position' => new FieldTypes\IntegerField(['allow_null' => true]),
                'fileId' => new FieldTypes\StringField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    /** @return array<string, mixed> */
    public function getEntityData(Download $entity): array {
        $file_ids = $entity->getStoredFileUploadIds();
        return [
            'name' => $entity->getName(),
            'position' => $entity->getPosition(),
            'fileId' => $file_ids[0] ?? null,
        ];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(Download $entity, array $input_data): void {
        $entity->setName($input_data['name']);
        $entity->setPosition(intval($input_data['position']));
        $entity->setFileId($input_data['fileId']);
    }

    /** @param array<string, mixed> $input_data */
    public function persistUploads(Download $entity, array $input_data): void {
        $this->persistOlzFiles($entity, [$input_data['fileId']]);
    }

    public function editUploads(Download $entity): void {
        $this->editOlzFiles($entity);
    }

    protected function getEntityById(int $id): Download {
        $repo = $this->entityManager()->getRepository(Download::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
