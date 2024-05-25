<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLocation;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeTerminTemplate extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
                $entity->setId(12);
                $entity->setNewsletter(true);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminTemplate();
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
                $entity->setImageIds(null);
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $termin_location = new TerminLocation();
                $termin_location->setId(12341);
                $entity = new TerminTemplate();
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
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
