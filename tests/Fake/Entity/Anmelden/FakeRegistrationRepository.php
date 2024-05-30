<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anmelden;

use Olz\Entity\Anmelden\Registration;
use Olz\Tests\Fake\Entity\Common\FakeOlzRepository;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;

class FakeRegistrationRepository extends FakeOlzRepository {
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object {
        if ($criteria === ['id' => FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setId(264);
            $registration->setOpensAt(new \DateTime('2020-03-13 15:00:00'));
            $registration->setClosesAt(new \DateTime('2020-03-16 09:00:00'));
            $registration->setTitle('Test title');
            $registration->setDescription('');
            $registration->setOwnerUser(FakeUser::adminUser());
            $registration->setOwnerRole(null);
            $registration->setOnOff(1);
            return $registration;
        }
        $criteria_json = json_encode($criteria);
        throw new \Exception("Query not mocked in findOneBy: {$criteria_json}", 1);
    }
}
