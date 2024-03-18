<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity;

use Olz\Entity\SolvPerson;
use Olz\Tests\Fake\Entity\Common\FakeEntity;

class FakeSolvPerson extends FakeEntity {
    public static function defaultSolvPerson($fresh = false) {
        return self::getFake(
            'default_solv_person',
            $fresh,
            function () {
                $solv_person = new SolvPerson();
                $solv_person->setId(1);
                $solv_person->setSameAs(null);
                $solv_person->setName('Test Runner');
                $solv_person->setBirthYear('08');
                $solv_person->setDomicile('Zürich ZH');
                $solv_person->setMember(1);
                return $solv_person;
            }
        );
    }
}
