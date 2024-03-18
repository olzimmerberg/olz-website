<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSolvEvent extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(12);
                $entity->setDate('2020-03-13');
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
                $entity->setLastModification('2020-01-11 21:43:58');
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            'empty',
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(123);
                $entity->setDate('2020-03-13');
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
                $entity->setLastModification('2020-01-11 21:43:58');
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            'maximal',
            $fresh,
            function () {
                $entity = new SolvEvent();
                $entity->setSolvUid(1234);
                $entity->setDate('2020-03-13');
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
                $entity->setLastModification('2020-01-11 21:43:58');
                return $entity;
            }
        );
    }

    public static function defaultSolvEvent($fresh = false) {
        return self::getFake(
            'default_solv_event',
            $fresh,
            function () {
                $solv_event = new SolvEvent();
                $solv_event->setSolvUid(1);
                $solv_event->setDate('2020-03-13');
                $solv_event->setDuration(1);
                $solv_event->setKind('foot');
                $solv_event->setDayNight('day');
                $solv_event->setNational(0);
                $solv_event->setRegion('ZH/SH');
                $solv_event->setType('*1');
                $solv_event->setName("Fake Event");
                $solv_event->setLink('https://staging.olzimmerberg.ch/');
                $solv_event->setClub('OL Zimmerberg');
                $solv_event->setMap('Landforst');
                $solv_event->setLocation('Pumpispitz');
                $solv_event->setCoordX(684376);
                $solv_event->setCoordY(236945);
                $solv_event->setDeadline(null);
                $solv_event->setEntryportal(2);
                $solv_event->setLastModification('2020-01-11 21:43:58');
                return $solv_event;
            }
        );
    }

    public static function withResults($fresh = false) {
        return self::getFake(
            'withResults',
            $fresh,
            function () {
                $solv_event = self::defaultSolvEvent(true);
                $solv_event->setSolvUid(20202);
                $solv_event->setName('Event with results');
                $solv_event->setLastModification('2020-01-11 21:48:36');
                $solv_event->setRankLink(1235);
                return $solv_event;
            }
        );
    }

    public static function withoutResults($fresh = false) {
        return self::getFake(
            'withoutResults',
            $fresh,
            function () {
                $solv_event = self::defaultSolvEvent(true);
                $solv_event->setSolvUid(20201);
                $solv_event->setName('Event without results');
                $solv_event->setLastModification('2020-01-11 21:36:48');
                return $solv_event;
            }
        );
    }
}
