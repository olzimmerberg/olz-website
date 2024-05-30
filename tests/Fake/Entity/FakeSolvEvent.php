<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSolvEvent extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(12);
                $entity->setDate(new \DateTime('2020-03-13'));
                $entity->setDuration(1);
                $entity->setKind('foot');
                $entity->setDayNight('day');
                $entity->setNational(0);
                $entity->setRegion('ZH/SH');
                $entity->setType('*1');
                $entity->setName("Fake Event");
                $entity->setLink('https://staging.olzimmerberg.ch/');
                $entity->setClub('OL Zimmerberg');
                $entity->setMap('Landforst');
                $entity->setLocation('Pumpispitz');
                $entity->setCoordX(684376);
                $entity->setCoordY(236945);
                $entity->setDeadline(null);
                $entity->setEntryportal(2);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(123);
                $entity->setDate(new \DateTime('2020-03-13'));
                $entity->setDuration(1);
                $entity->setKind('foot');
                $entity->setDayNight('day');
                $entity->setNational(0);
                $entity->setRegion('ZH/SH');
                $entity->setType('*1');
                $entity->setName("Fake Event");
                $entity->setLink('https://staging.olzimmerberg.ch/');
                $entity->setClub('OL Zimmerberg');
                $entity->setMap('Landforst');
                $entity->setLocation('Pumpispitz');
                $entity->setCoordX(684376);
                $entity->setCoordY(236945);
                $entity->setDeadline(new \DateTime('1970-01-01 00:00:00'));
                $entity->setEntryportal(2);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(1234);
                $entity->setDate(new \DateTime('2020-03-13'));
                $entity->setDuration(1);
                $entity->setKind('foot');
                $entity->setDayNight('day');
                $entity->setNational(0);
                $entity->setRegion('ZH/SH');
                $entity->setType('*1');
                $entity->setName("Fake Event");
                $entity->setLink('https://staging.olzimmerberg.ch/');
                $entity->setClub('OL Zimmerberg');
                $entity->setMap('Landforst');
                $entity->setLocation('Pumpispitz');
                $entity->setCoordX(684376);
                $entity->setCoordY(236945);
                $entity->setDeadline(new \DateTime('2020-03-13 19:30:00'));
                $entity->setEntryportal(2);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function defaultSolvEvent($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(1);
                $entity->setDate(new \DateTime('2020-03-13'));
                $entity->setDuration(1);
                $entity->setKind('foot');
                $entity->setDayNight('day');
                $entity->setNational(0);
                $entity->setRegion('ZH/SH');
                $entity->setType('*1');
                $entity->setName("Fake Event");
                $entity->setLink('https://staging.olzimmerberg.ch/');
                $entity->setClub('OL Zimmerberg');
                $entity->setMap('Landforst');
                $entity->setLocation('Pumpispitz');
                $entity->setCoordX(684376);
                $entity->setCoordY(236945);
                $entity->setDeadline(null);
                $entity->setEntryportal(2);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function withResults($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultSolvEvent(true);
                $entity->setSolvUid(20202);
                $entity->setName('Event with results');
                $entity->setRankLink('https://o-l.ch?rang=1235');
                $entity->setLastModification(new \DateTime('2020-01-11 21:48:36'));
                return $entity;
            }
        );
    }

    public static function withoutResults($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $entity = self::defaultSolvEvent(true);
                $entity->setSolvUid(20201);
                $entity->setName('Event without results');
                $entity->setLastModification(new \DateTime('2020-01-11 21:36:48'));
                return $entity;
            }
        );
    }
}
