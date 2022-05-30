<?php

declare(strict_types=1);

require_once __DIR__.'/../../../../_/anmelden/endpoints/CreateBookingEndpoint.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeCreateBookingEndpointRegistrationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setId(264);
            return $registration;
        }
        $where_json = json_encode($where);
        throw new Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

class FakeCreateBookingEndpointRegistrationInfoRepository {
    public function findOneBy($where) {
        if ($where['ident'] === '0-vorname') {
            return new RegistrationInfo();
        }
        if ($where['ident'] === '1-nachname') {
            return new RegistrationInfo();
        }
        $where_json = json_encode($where);
        throw new Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 * @covers \CreateBookingEndpoint
 */
final class CreateBookingEndpointTest extends UnitTestCase {
    public function testCreateBookingEndpointIdent(): void {
        $endpoint = new CreateBookingEndpoint();
        $this->assertSame('CreateBookingEndpoint', $endpoint->getIdent());
    }

    public function testCreateBookingEndpoint(): void {
        $entity_manager = new FakeEntityManager();
        $registration_repo = new FakeCreateBookingEndpointRegistrationRepository();
        $entity_manager->repositories['Registration'] = $registration_repo;
        $registration_info_repo = new FakeCreateBookingEndpointRegistrationInfoRepository();
        $entity_manager->repositories['RegistrationInfo'] = $registration_info_repo;
        $auth_utils = new FakeAuthUtils();
        $auth_utils->has_permission_by_query = ['any' => true];
        $entity_utils = new FakeEntityUtils();
        $logger = FakeLogger::create();
        $endpoint = new CreateBookingEndpoint();
        $endpoint->setAuthUtils($auth_utils);
        $endpoint->setDateUtils(new FixedDateUtils('2020-03-13 19:30:00'));
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setEntityUtils($entity_utils);
        $endpoint->setIdUtils(new FakeIdUtils());
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'meta' => [
                'onOff' => true,
                'ownerUserId' => null,
                'ownerRoleId' => null,
            ],
            'data' => [
                'registrationId' => 'Registration:'.FakeEntityManager::AUTO_INCREMENT_ID,
                'values' => [
                    '0-vorname' => 'Simon',
                    '1-nachname' => 'Hatt',
                ],
            ],
        ]);

        $this->assertSame([
            'status' => 'OK',
            'id' => 'Booking:'.FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $booking = $entity_manager->persisted[0];
        $this->assertSame(FakeEntityManager::AUTO_INCREMENT_ID, $booking->getId());
        $this->assertSame(264, $booking->getRegistration()->getId());
        $this->assertSame('{"0-vorname":"Simon","1-nachname":"Hatt"}', $booking->getFormData());
        $this->assertSame(FakeUsers::adminUser(), $booking->getUser());

        $this->assertSame([
            [$booking, 1, null, null],
        ], $entity_utils->create_olz_entity_calls);
    }
}
