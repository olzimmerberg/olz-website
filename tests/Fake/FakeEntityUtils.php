<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\Common\OlzEntity;
use Olz\Utils\EntityUtils;

class FakeEntityUtils extends EntityUtils {
    /** @var array<array{0: OlzEntity, 1: int, 2: ?int, 3: ?int}> */
    public array $create_olz_entity_calls = [];
    /** @var array<array{0: OlzEntity, 1: int, 2: ?int, 3: ?int}> */
    public array $update_olz_entity_calls = [];
    public ?bool $can_update_olz_entity = null;

    public function createOlzEntity(OlzEntity $entity, array $input): void {
        parent::createOlzEntity($entity, $input);
        $this->create_olz_entity_calls[] = [
            $entity,
            $entity->getOnOff() ? 1 : 0,
            $entity->getOwnerUser()?->getId() ?? null,
            $entity->getOwnerRole()?->getId() ?? null,
        ];
    }

    public function updateOlzEntity(OlzEntity $entity, array $input): void {
        parent::updateOlzEntity($entity, $input);
        $this->update_olz_entity_calls[] = [
            $entity,
            $entity->getOnOff() ? 1 : 0,
            $entity->getOwnerUser()?->getId() ?? null,
            $entity->getOwnerRole()?->getId() ?? null,
        ];
    }

    /** @param ?array{onOff?: bool, ownerUserId?: int, ownerRoleId?: int} $meta_arg */
    public function canUpdateOlzEntity(
        ?OlzEntity $entity,
        ?array $meta_arg,
        string $edit_permission = 'all',
    ): bool {
        if ($this->can_update_olz_entity === null) {
            throw new \Exception("FakeEntityUtils::canUpdateOlzEntity not mocked");
        }
        return $this->can_update_olz_entity;
    }
}
