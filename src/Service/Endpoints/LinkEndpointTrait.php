<?php

namespace Olz\Service\Endpoints;

use Olz\Entity\Service\Link;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait LinkEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return false;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzLinkDataOrNull' : 'OlzLinkData',
            'field_structure' => [
                'position' => new FieldTypes\IntegerField(['allow_null' => true]),
                'name' => new FieldTypes\StringField([]),
                'url' => new FieldTypes\StringField([]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    public function getEntityData(Link $entity): array {
        return [
            'name' => $entity->getName(),
            'position' => $entity->getPosition(),
            'url' => $entity->getUrl(),
        ];
    }

    public function updateEntityWithData(Link $entity, array $input_data): void {
        $entity->setName($input_data['name']);
        $entity->setPosition(intval($input_data['position']));
        $entity->setUrl($input_data['url']);
    }

    protected function getEntityById(int $id): Link {
        $link_repo = $this->entityManager()->getRepository(Link::class);
        $link = $link_repo->findOneBy(['id' => $id]);
        if (!$link) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $link;
    }
}
