<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\ForwardedEmail;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<ForwardedEmail>
 */
class FakeForwardedEmail extends FakeEntity {
    public static function minimal(bool $fresh = false): ForwardedEmail {
        return self::getFake(
            $fresh,
            function () {
                $entity = new ForwardedEmail();
                $entity->setId(12);
                $entity->setRecipientUser(null);
                $entity->setSenderAddress('');
                $entity->setSubject('');
                $entity->setBody('');
                $entity->setForwardedAt(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): ForwardedEmail {
        return self::getFake(
            $fresh,
            function () {
                $entity = new ForwardedEmail();
                $entity->setId(123);
                $entity->setRecipientUser(null);
                $entity->setSenderAddress('');
                $entity->setSubject('');
                $entity->setBody('');
                $entity->setForwardedAt(new \DateTime('0000-00-00 00:00:00'));
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): ForwardedEmail {
        return self::getFake(
            $fresh,
            function () {
                $entity = new ForwardedEmail();
                $entity->setId(1234);
                $entity->setRecipientUser(FakeUser::maximal());
                $entity->setSenderAddress('Fake Purpose');
                $entity->setSubject('ABC123abc');
                $entity->setBody('ABC123abc');
                $entity->setForwardedAt(new \DateTime('2020-03-13 19:30:00'));
                return $entity;
            }
        );
    }
}
