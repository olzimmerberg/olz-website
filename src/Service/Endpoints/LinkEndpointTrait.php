<?php

namespace Olz\Service\Endpoints;

use Olz\Entity\Service\Link;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzLinkId int
 * @phpstan-type OlzLinkData array{
 *   position?: ?int,
 *   name: non-empty-string,
 *   url: non-empty-string,
 * }
 */
trait LinkEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzLinkData */
    public function getEntityData(Link $entity): array {
        return [
            'name' => $entity->getName() ?: '-',
            'position' => $entity->getPosition(),
            'url' => $entity->getUrl() ?: '-',
        ];
    }

    /** @param OlzLinkData $input_data */
    public function updateEntityWithData(Link $entity, array $input_data): void {
        $entity->setName($input_data['name']);
        $entity->setPosition($input_data['position'] ?? 0);
        $entity->setUrl($input_data['url']);
    }

    protected function getEntityById(int $id): Link {
        $repo = $this->entityManager()->getRepository(Link::class);
        $entity = $repo->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new HttpError(404, "Nicht gefunden.");
        }
        return $entity;
    }
}
