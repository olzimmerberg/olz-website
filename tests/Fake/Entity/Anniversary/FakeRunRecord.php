<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anniversary;

use Olz\Entity\Anniversary\RunRecord;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;
use Olz\Tests\Fake\Entity\Users\FakeUser;

/**
 * @extends FakeEntity<RunRecord>
 */
class FakeRunRecord extends FakeEntity {
    public static function minimal(bool $fresh = false): RunRecord {
        return self::getFake(
            $fresh,
            function () {
                $entity = new RunRecord();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setUser(null);
                $entity->setRunnerName('Required N.');
                $entity->setRunAt(new \DateTime('2020-08-15 16:27:00'));
                $entity->setDistanceMeters(0);
                $entity->setElevationMeters(0);
                $entity->setSportType(null);
                $entity->setSource(null);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): RunRecord {
        return self::getFake(
            $fresh,
            function () {
                $entity = new RunRecord();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setUser(FakeUser::empty());
                $entity->setRunnerName('');
                $entity->setRunAt(new \DateTime('0000-00-00 00:00:00'));
                $entity->setDistanceMeters(0);
                $entity->setElevationMeters(0);
                $entity->setSportType('');
                $entity->setSource('');
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): RunRecord {
        return self::getFake(
            $fresh,
            function () {
                $entity = new RunRecord();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setUser(FakeUser::maximal());
                $entity->setRunnerName('Max M.');
                $entity->setRunAt(new \DateTime('2020-08-15 16:27:00'));
                $entity->setDistanceMeters(3000);
                $entity->setElevationMeters(200);
                $entity->setSportType('Maximal Run');
                $entity->setSource('shady_source');
                return $entity;
            }
        );
    }
}
