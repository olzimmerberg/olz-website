<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\GetRegistrationEndpoint;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeGetRegistrationEndpointRegistrationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setTitle('Test title');
            $registration->setDescription('');
            $registration->setOwnerUser(Fake\FakeUsers::adminUser());
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
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\GetRegistrationEndpoint
 */
final class GetRegistrationEndpointTest extends UnitTestCase {
    public function testGetRegistrationEndpointIdent(): void {
        $endpoint = new GetRegistrationEndpoint();
        $this->assertSame('GetRegistrationEndpoint', $endpoint->getIdent());
    }

    public function testGetRegistrationEndpoint(): void {
        $entity_manager = new Fake\FakeEntityManager();
        $registration_repo = new FakeGetRegistrationEndpointRegistrationRepository();
        $entity_manager->repositories[Registration::class] = $registration_repo;
        $registration_info_repo = new FakeGetRegistrationEndpointRegistrationInfoRepository();
        $entity_manager->repositories[RegistrationInfo::class] = $registration_info_repo;
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $logger = Fake\FakeLogger::create();
        $endpoint = new GetRegistrationEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'id' => 'Registration:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ]);

        $this->assertSame([
            'id' => 'Registration:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
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
