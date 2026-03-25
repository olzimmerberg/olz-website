<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminReaction;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<TerminReaction>
 */
class FakeTerminReaction extends FakeEntity {
    public static function minimal(bool $fresh = false): TerminReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminReaction();
                $entity->setId(12);
                $entity->setTermin(FakeTermin::minimal());
                $entity->setUser(FakeUser::minimal());
                $entity->setEmoji('🚫');
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): TerminReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminReaction();
                $entity->setId(123);
                $entity->setTermin(FakeTermin::empty());
                $entity->setUser(FakeUser::empty());
                $entity->setEmoji('⭕');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): TerminReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminReaction();
                $entity->setId(1234);
                $entity->setTermin(FakeTermin::maximal());
                $entity->setUser(FakeUser::maximal());
                $entity->setEmoji('❎');
                return $entity;
            }
        );
    }
}
