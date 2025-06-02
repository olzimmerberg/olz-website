<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\Date;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

/**
 * @extends FakeEntity<SolvEvent>
 */
class FakeSolvEvent extends FakeEntity {
    public static function minimal(bool $fresh = false): SolvEvent {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(12);
                $entity->setDate(new Date('1970-01-01'));
                $entity->setDuration(-1);
                $entity->setKind('');
                $entity->setDayNight('');
                $entity->setNational(-1);
                $entity->setRegion('');
                $entity->setType('');
                $entity->setName("");
                $entity->setLink('');
                $entity->setClub('');
                $entity->setMap('');
                $entity->setLocation('');
                $entity->setCoordX(-1);
                $entity->setCoordY(-1);
                $entity->setDeadline(null);
                $entity->setEntryportal(-1);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): SolvEvent {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(123);
                $entity->setDate(new Date('1970-01-01'));
                $entity->setDuration(0);
                $entity->setKind('');
                $entity->setDayNight('');
                $entity->setNational(0);
                $entity->setRegion('');
                $entity->setType('');
                $entity->setName("");
                $entity->setLink('');
                $entity->setClub('');
                $entity->setMap('');
                $entity->setLocation('');
                $entity->setCoordX(0);
                $entity->setCoordY(0);
                $entity->setDeadline(new Date('1970-01-01'));
                $entity->setEntryportal(0);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('1970-01-01 00:00:00'));
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): SolvEvent {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(1234);
                $entity->setDate(new Date('2020-03-13'));
                $entity->setDuration(3);
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
                $entity->setDeadline(new Date('2020-03-13'));
                $entity->setEntryportal(2);
                $entity->setRankLink(null);
                $entity->setLastModification(new \DateTime('2020-01-11 21:43:58'));
                return $entity;
            }
        );
    }

    public static function defaultSolvEvent(bool $fresh = false): SolvEvent {
        return self::getFake(
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(1);
                $entity->setDate(new Date('2020-03-13'));
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

    public static function withResults(bool $fresh = false): SolvEvent {
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

    public static function withoutResults(bool $fresh = false): SolvEvent {
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
