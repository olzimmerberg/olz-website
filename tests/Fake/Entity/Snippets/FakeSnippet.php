<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Snippets;

use Olz\Entity\Snippets\Snippet;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSnippet extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                $entity->setId(12);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                $entity->setId(123);
                $entity->setText('');
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Snippet();
                $entity->setId(1234);
                $entity->setText('test-text');
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
