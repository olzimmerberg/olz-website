<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\News;

use Olz\Entity\News\NewsReaction;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<NewsReaction>
 */
class FakeNewsReaction extends FakeEntity {
    public static function minimal(bool $fresh = false): NewsReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsReaction();
                $entity->setId(12);
                $entity->setNewsEntry(FakeNews::minimal());
                $entity->setUser(FakeUser::minimal());
                $entity->setEmoji('🚫');
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): NewsReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsReaction();
                $entity->setId(123);
                $entity->setNewsEntry(FakeNews::empty());
                $entity->setUser(FakeUser::empty());
                $entity->setEmoji('⭕');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): NewsReaction {
        return self::getFake(
            $fresh,
            function () {
                $entity = new NewsReaction();
                $entity->setId(1234);
                $entity->setNewsEntry(FakeNews::maximal());
                $entity->setUser(FakeUser::maximal());
                $entity->setEmoji('❎');
                return $entity;
            }
        );
    }
}
