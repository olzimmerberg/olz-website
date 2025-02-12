<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Snippets;

use Olz\Entity\Snippets\Snippet;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<Snippet>
 */
class FakeSnippet extends FakeEntity {
    public static function minimal(bool $fresh = false): Snippet {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setText(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Snippet {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setText('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): Snippet {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setText('test-text');
                return $entity;
            }
        );
    }
}
