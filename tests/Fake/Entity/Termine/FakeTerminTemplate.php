<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<TerminTemplate>
 */
class FakeTerminTemplate extends FakeEntity {
    public static function minimal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setStartTime(null);
                $entity->setDurationSeconds(null);
                $entity->setTitle(null);
                $entity->setText(null);
                $entity->setDeadlineEarlierSeconds(null);
                $entity->setDeadlineTime(null);
                $entity->setNewsletter(true);
                $entity->setTypes(null);
                $entity->setLocation(null);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setStartTime(null);
                $entity->setDurationSeconds(null);
                $entity->setTitle(null);
                $entity->setText(null);
                $entity->setDeadlineEarlierSeconds(null);
                $entity->setDeadlineTime(null);
                $entity->setNewsletter(false);
                $entity->setTypes(null);
                $entity->setLocation(null);
                $entity->setImageIds([]);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): object {
        return self::getFake(
            $fresh,
            function () {
                $termin_location = new TerminLocation();
                $termin_location->setId(12341);
                $entity = new TerminTemplate();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setStartTime(new \DateTime('09:00:00'));
                $entity->setDurationSeconds(7200);
                $entity->setTitle("Fake title");
                $entity->setText("Fake text");
                $entity->setDeadlineEarlierSeconds(86400 * 2);
                $entity->setDeadlineTime(new \DateTime('18:00:00'));
                $entity->setNewsletter(true);
                $entity->setTypes(' ol club ');
                $entity->setLocation($termin_location);
                $entity->setImageIds([
                    'image__________________1.jpg', 'image__________________2.png']);
                return $entity;
            }
        );
    }
}
