<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Service;

use Olz\Entity\Service\Download;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<Download>
 */
class FakeDownload extends FakeEntity {
    public static function minimal(bool $fresh = false): Download {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setName('Fake Download');
                $entity->setPosition(12);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): Download {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setName('Fake Download');
                $entity->setPosition(123);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): Download {
        return self::getFake(
            $fresh,
            function () {
                $entity = new Download();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setName('Fake Download');
                $entity->setPosition(1234);
                return $entity;
            }
        );
    }
}
