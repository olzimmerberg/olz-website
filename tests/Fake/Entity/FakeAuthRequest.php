<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AuthRequest;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

/**
 * @extends FakeEntity<AuthRequest>
 */
class FakeAuthRequest extends FakeEntity {
    public static function defaultAuthRequest(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AuthRequest();
                $entity->setId(1);
                return $entity;
            }
        );
    }
}
