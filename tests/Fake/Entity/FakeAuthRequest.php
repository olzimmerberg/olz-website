<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeAuthRequest extends FakeEntity {
    public static function defaultAuthRequest($fresh = false) {
        return self::getFake(
            'default_auth_request',
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(1);
                return $entity;
            }
        );
    }
}
