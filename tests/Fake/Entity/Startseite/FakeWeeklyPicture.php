<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Startseite;

use Olz\Entity\Startseite\WeeklyPicture;
use Olz\Tests\Fake\Entity\Common\Date;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<WeeklyPicture>
 */
class FakeWeeklyPicture extends FakeEntity {
    public static function minimal(bool $fresh = false): WeeklyPicture {
        return self::getFake(
            $fresh,
            function () {
                $entity = new WeeklyPicture();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setPublishedDate(null);
                $entity->setText(null);
                $entity->setImageId(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): WeeklyPicture {
        return self::getFake(
            $fresh,
            function () {
                $published_at = new Date('0000-01-01');
                $entity = new WeeklyPicture();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setPublishedDate($published_at);
                $entity->setText('');
                $entity->setImageId('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): WeeklyPicture {
        return self::getFake(
            $fresh,
            function () {
                $published_at = new Date('2020-03-13');
                $entity = new WeeklyPicture();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setPublishedDate($published_at);
                $entity->setText('Fake text');
                $entity->setImageId('image__________________1.jpg');
                return $entity;
            }
        );
    }
}
