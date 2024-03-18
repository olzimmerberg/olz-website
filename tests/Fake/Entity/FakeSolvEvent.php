<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvEvent;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSolvEvent extends FakeEntity {
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
}
