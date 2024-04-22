<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anmelden;

use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;

class FakeRegistrationInfoRepository extends FakeOlzRepository {
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria['ident'] === '0-vorname') {
            return new RegistrationInfo();
        }
        if ($criteria['ident'] === '1-nachname') {
            return new RegistrationInfo();
        }
        $criteria_json = json_encode($criteria);
        throw new \Exception("Query not mocked in findOneBy: {$criteria_json}", 1);
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null): array {
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
