<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\AccessToken;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<AccessToken>
 */
class FakeAccessToken extends FakeEntity {
    public static function default(bool $fresh = false): AccessToken {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AccessToken();
                $entity->setId(1);
                $entity->setUser(FakeUser::defaultUser());
                $entity->setPurpose('Fake Purpose');
                $entity->setToken('ABC123abc');
                $entity->setCreatedAt(new \DateTime('2020-03-13 19:30:00'));
                $entity->setExpiresAt(new \DateTime('2021-03-13 19:30:00'));
                return $entity;
            }
        );
    }

    public static function valid(bool $fresh = false): AccessToken {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AccessToken();
                $entity->setId(2);
                $entity->setToken('valid-token');
                $entity->setUser(FakeUser::adminUser());
                $entity->setPurpose('Valid Purpose');
                $entity->setExpiresAt(new \DateTime('2022-01-24 00:00:00'));
                return $entity;
            }
        );
    }

    public static function expired(bool $fresh = false): AccessToken {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AccessToken();
                $entity->setId(3);
                $entity->setToken('expired-token');
                $entity->setUser(FakeUser::adminUser());
                $entity->setPurpose('Expired Purpose');
                $entity->setExpiresAt(new \DateTime('2020-01-11 20:00:00'));
                return $entity;
            }
        );
    }

    public static function webDav(bool $fresh = false): AccessToken {
        return self::getFake(
            $fresh,
            function () {
                $entity = new AccessToken();
                $entity->setId(4);
                $entity->setToken('webdav-token');
                $entity->setUser(FakeUser::adminUser());
                $entity->setPurpose('WebDAV');
                $entity->setExpiresAt(new \DateTime('2022-01-24 00:00:00'));
                return $entity;
            }
        );
    }
}
