<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvResult;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSolvResult extends FakeEntity {
    public static function defaultSolvResult($fresh = false) {
        return self::getFake(
            $fresh,
            function () {
                $solv_result = new SolvResult();
                $solv_result->setId(1);
                $solv_result->setPerson(12);
                $solv_result->setEvent(1);
                $solv_result->setClass('H12');
                $solv_result->setName('Test Runner');
                $solv_result->setBirthYear('08');
                $solv_result->setDomicile('Zürich ZH');
                $solv_result->setClub('OL Zimmerberg');
                $solv_result->setRank(4);
                $solv_result->setResult(3795); // 1:03:15
                $solv_result->setSplits('gibberish');
                $solv_result->setFinishSplit(23); // 0:00:23
                $solv_result->setClassDistance(3500); // 3.5 km
                $solv_result->setClassElevation(125); // 125 m
                $solv_result->setClassControlCount(16);
                $solv_result->setClassCompetitorCount(71);
                return $solv_result;
            }
        );
    }
}
