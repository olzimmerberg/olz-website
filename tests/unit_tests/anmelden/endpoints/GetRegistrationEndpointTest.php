<?php

declare(strict_types=1);

use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\FixedDateUtils;

require_once __DIR__.'/../../../../_/anmelden/endpoints/GetRegistrationEndpoint.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeGetRegistrationEndpointRegistrationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setTitle('Test title');
            $registration->setDescription('');
            $registration->setOwnerUser(FakeUsers::adminUser());
            $registration->setOwnerRole(null);
            $registration->setOnOff(true);
            return $registration;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

class FakeGetRegistrationEndpointRegistrationInfoRepository {
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

/**
 * @internal
 * @covers \GetRegistrationEndpoint
 */
final class GetRegistrationEndpointTest extends UnitTestCase {
    public function testGetRegistrationEndpointIdent(): void {
        $endpoint = new GetRegistrationEndpoint();
        $this->assertSame('GetRegistrationEndpoint', $endpoint->getIdent());
    }

    public function testGetRegistrationEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $registration_repo = new FakeGetRegistrationEndpointRegistrationRepository();
        $entity_manager->repositories[Registration::class] = $registration_repo;
        $registration_info_repo = new FakeGetRegistrationEndpointRegistrationInfoRepository();
        $entity_manager->repositories[RegistrationInfo::class] = $registration_info_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $logger = FakeLogger::create();
        $endpoint = new GetRegistrationEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'id' => 'Registration:'.FakeEntityManager::AUTO_INCREMENT_ID,
        ]);

        $this->assertSame([
            'id' => 'Registration:'.FakeEntityManager::AUTO_INCREMENT_ID,
            'meta' => [
                'ownerUserId' => 2,
                'ownerRoleId' => null,
                'onOff' => true,
            ],
            'data' => [
                'title' => 'Test title',
                'description' => '',
                'infos' => [
                    [
                        'type' => 'string',
                        'isOptional' => false,
                        'title' => 'Test Info 1',
                        'description' => '',
                        'options' => null,
                    ],
                    [
                        'type' => 'string',
                        'isOptional' => true,
                        'title' => 'Test Info 2',
                        'description' => '',
                        'options' => null,
                    ],
                ],
                'opensAt' => null,
                'closesAt' => null,
            ],
        ], $result);
        $this->assertSame(0, count($entity_manager->persisted));
        $this->assertSame(0, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
    }
}
