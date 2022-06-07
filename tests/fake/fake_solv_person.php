<?php

use Olz\Entity\SolvPerson;

function get_fake_solv_person() {
    $solv_person = new SolvPerson();
    $solv_person->setId(1);
    $solv_person->setSameAs(null);
    $solv_person->setName('Test Runner');
    $solv_person->setBirthYear('08');
    $solv_person->setDomicile('Zürich ZH');
    $solv_person->setMember(1);
    return $solv_person;
}
