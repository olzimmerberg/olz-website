<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Termine;

use Olz\Entity\Termine\TerminLabel;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeTerminLabel extends FakeEntity {
    public static function minimal($fresh = false) {
        return self::getFake(
            'minimal',
            $fresh,
            function () {
                $entity = new TerminLabel();
                $entity->setId(12);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }

    public static function empty($fresh = false) {
        return self::getFake(
            'empty',
            $fresh,
            function () {
                $entity = new TerminLabel();
                $entity->setId(123);
                $entity->setIdent('');
                $entity->setName('');
                $entity->setDetails('');
                $entity->setIcon(null);
                $entity->setPosition(0);
                $entity->setOnOff(false);
                return $entity;
            }
        );
    }

    public static function maximal($fresh = false) {
        return self::getFake(
            'maximal',
            $fresh,
            function () {
                $entity = new TerminLabel();
                $entity->setId(1234);
                $entity->setIdent('test-label');
                $entity->setName('Test Termin-Label');
                $entity->setDetails('Test Termin-Label Detail');
                $entity->setIcon('aaaaaaaaaaaaaaaaaaaaaaaa.svg');
                $entity->setPosition(1234);
                $entity->setOnOff(true);
                return $entity;
            }
        );
    }
}
