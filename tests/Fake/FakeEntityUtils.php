<?php

declare(strict_types=1);

namespace Olz\Tests\Fake;

use Olz\Entity\Common\OlzEntity;
use Olz\Utils\EntityUtils;

class FakeEntityUtils extends EntityUtils {
    public $create_olz_entity_calls = [];
    public $update_olz_entity_calls = [];
    public $can_update_olz_entity;

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

    public function canUpdateOlzEntity(
        OlzEntity $entity,
        $meta_arg,
        $edit_permission = 'all',
    ): bool {
        if ($this->can_update_olz_entity === null) {
            throw new \Exception("FakeEntityUtils::canUpdateOlzEntity not mocked");
        }
        return $this->can_update_olz_entity;
    }
}
