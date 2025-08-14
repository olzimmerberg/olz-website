<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLabel;
use Olz\Tests\Fake\Entity\Common\FakeEntity;
use Olz\Tests\Fake\Entity\Common\FakeOlzEntity;

/**
 * @extends FakeEntity<TerminLabel>
 */
class FakeTerminLabel extends FakeEntity {
    public static function minimal(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::minimal($entity);
                $entity->setId(12);
                $entity->setIdent('');
                $entity->setName('');
                $entity->setDetails(null);
                $entity->setIcon(null);
                $entity->setPosition(0);
                return $entity;
            }
        );
    }

    public static function empty(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::empty($entity);
                $entity->setId(123);
                $entity->setIdent('');
                $entity->setName('');
                $entity->setDetails('');
                $entity->setIcon('');
                $entity->setPosition(0);
                return $entity;
            }
        );
    }

    public static function maximal(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::maximal($entity);
                $entity->setId(1234);
                $entity->setIdent('test-label');
                $entity->setName('Test Termin-Label');
                $entity->setDetails('Test Termin-Label Detail');
                $entity->setIcon('aaaaaaaaaaaaaaaaaaaaaaaa.svg');
                $entity->setPosition(1234);
                return $entity;
            }
        );
    }

    public static function weekend(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::maximal($entity);
                $entity->setId(2);
                $entity->setIdent('weekend');
                $entity->setName('Weekends');
                $entity->setDetails('');
                $entity->setIcon(null);
                $entity->setPosition(1);
                return $entity;
            }
        );
    }

    public static function training(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::maximal($entity);
                $entity->setId(3);
                $entity->setIdent('training');
                $entity->setName('Trainings');
                $entity->setDetails('Kartentrainings, Hallentrainings, Longjoggs');
                $entity->setIcon(null);
                $entity->setPosition(2);
                return $entity;
            }
        );
    }

    public static function ol(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::maximal($entity);
                $entity->setId(5);
                $entity->setIdent('ol');
                $entity->setName('Wettkämpfe');
                $entity->setDetails('');
                $entity->setIcon(null);
                $entity->setPosition(4);
                return $entity;
            }
        );
    }

    public static function club(bool $fresh = false): TerminLabel {
        return self::getFake(
            $fresh,
            function () {
                $entity = new TerminLabel();
                FakeOlzEntity::maximal($entity);
                $entity->setId(6);
                $entity->setIdent('club');
                $entity->setName('Vereinsanlässe');
                $entity->setDetails('');
                $entity->setIcon(null);
                $entity->setPosition(5);
                return $entity;
            }
        );
    }
}
