<?php

declare(strict_types=1);

namespace Olz\Tests\Fake\Entity\Anmelden;

use Olz\Entity\Anmelden\Registration;
use Olz\Tests\Fake\Entity\FakeUser;
use Olz\Tests\Fake\FakeEntityManager;

class FakeRegistrationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setId(264);
            $registration->setTitle('Test title');
            $registration->setDescription('');
            $registration->setOwnerUser(FakeUser::adminUser());
            $registration->setOwnerRole(null);
            $registration->setOnOff(true);
            return $registration;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}
