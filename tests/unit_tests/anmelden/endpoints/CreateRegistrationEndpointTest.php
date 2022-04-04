<?php

declare(strict_types=1);

use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/anmelden/endpoints/CreateRegistrationEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeIdUtils.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

/**
 * @internal
 * @covers \CreateRegistrationEndpoint
 */
final class CreateRegistrationEndpointTest extends UnitTestCase {
    public function testCreateRegistrationEndpointIdent(): void {
        $endpoint = new CreateRegistrationEndpoint();
        $this->assertSame('CreateRegistrationEndpoint', $endpoint->getIdent());
    }

    public function testCreateRegistrationEndpointNoAccess(): void {
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => false];
        $env_utils = new FakeEnvUtils();
        $logger = FakeLogger::create();
        $endpoint = new CreateRegistrationEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setEnvUtils($env_utils);
        $endpoint->setLogger($logger);

        try {
            $endpoint->call([
                'title' => 'Test Titel',
                'description' => 'Test Description',
                'infos' => [],
                'opensAt' => null,
                'closesAt' => null,
                'ownerUserId' => 1,
                'ownerRoleId' => 1,
                'onOff' => true,
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testCreateRegistrationEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_utils = new FakeEntityUtils();
        $logger = FakeLogger::create();
        $endpoint = new CreateRegistrationEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'title' => 'Training',
            'description' => 'Training vom 17.3.2020 im Landforst.',
            'infos' => [
                [
                    'title' => 'Vorname',
                    'description' => '',
                    'type' => 'firstName',
                    'isOptional' => false,
                    'options' => null,
                ],
                [
                    'title' => 'Nachname',
                    'description' => '',
                    'type' => 'lastName',
                    'isOptional' => false,
                    'options' => null,
                ],
            ],
            'opensAt' => null,
            'closesAt' => null,
            'ownerUserId' => 1,
            'ownerRoleId' => 1,
            'onOff' => true,
        ]);

        $this->assertSame([
            'status' => 'OK',
            'registrationId' => 'Registration:'.FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(3, count($entity_manager->persisted));
        $this->assertSame(3, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $registration = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $registration->getId());
        $this->assertSame('Training', $registration->getTitle());
        $this->assertSame('Training vom 17.3.2020 im Landforst.', $registration->getDescription());
        $registration_info_1 = $entity_manager->persisted[1];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $registration_info_1->getId());
        $this->assertSame('Vorname', $registration_info_1->getTitle());
        $this->assertSame('', $registration_info_1->getDescription());
        $registration_info_2 = $entity_manager->persisted[2];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $registration_info_2->getId());
        $this->assertSame('Nachname', $registration_info_2->getTitle());
        $this->assertSame('', $registration_info_2->getDescription());

        $this->assertSame([
            [$registration, 1, 1, 1],
            [$registration_info_1, 1, 1, 1],
            [$registration_info_2, 1, 1, 1],
        ], $entity_utils->create_olz_entity_calls);
    }
}
