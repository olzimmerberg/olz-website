<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anmelden;

use Olz\Entity\Anmelden\RegistrationInfo;

class FakeRegistrationInfoRepository {
    public function findOneBy($where) {
        if ($where['ident'] === '0-vorname') {
            return new RegistrationInfo();
        }
        if ($where['ident'] === '1-nachname') {
            return new RegistrationInfo();
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }

    public function findBy($where) {
        $registration_info_1 = new RegistrationInfo();
        $registration_info_1->setType('string');
        $registration_info_1->setIsOptional(false);
        $registration_info_1->setTitle('Test Info 1');
        $registration_info_1->setDescription('');
        $registration_info_2 = new RegistrationInfo();
        $registration_info_2->setType('string');
        $registration_info_2->setIsOptional(true);
        $registration_info_2->setTitle('Test Info 2');
        $registration_info_2->setDescription('');
        return [$registration_info_1, $registration_info_2];
    }
}
