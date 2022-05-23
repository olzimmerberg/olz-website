<?php

declare(strict_types=1);

require_once __DIR__.'/../../public/_/config/vendor/autoload.php';

class FakeEntityUtils {
    public $create_olz_entity_calls = [];
    public $update_olz_entity_calls = [];

    public function createOlzEntity(OlzEntity $entity, $input) {
        $this->create_olz_entity_calls[] = [
            $entity,
            ($input['onOff'] ?? null) ? 1 : 0,
            $input['ownerUserId'] ?? null,
            $input['ownerRoleId'] ?? null,
        ];
    }

    public function updateOlzEntity(OlzEntity $entity, $input) {
        $this->update_olz_entity_calls[] = [
            $entity,
            ($input['onOff'] ?? null) ? 1 : 0,
            $input['ownerUserId'] ?? null,
            $input['ownerRoleId'] ?? null,
        ];
    }
}
