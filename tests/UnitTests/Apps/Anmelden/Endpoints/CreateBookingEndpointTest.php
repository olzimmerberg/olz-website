<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\CreateBookingEndpoint;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

class FakeCreateBookingEndpointRegistrationRepository {
    public function findOneBy($where) {
        if ($where === ['id' => Fake\FakeEntityManager::AUTO_INCREMENT_ID]) {
            $registration = new Registration();
            $registration->setId(264);
            return $registration;
        }
        $where_json = json_encode($where);
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
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
        throw new \Exception("Query not mocked in findOneBy: {$where_json}", 1);
    }
}

/**
 * @internal
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\CreateBookingEndpoint
 */
final class CreateBookingEndpointTest extends UnitTestCase {
    public function testCreateBookingEndpointIdent(): void {
        $endpoint = new CreateBookingEndpoint();
        $this->assertSame('CreateBookingEndpoint', $endpoint->getIdent());
    }

    public function testCreateBookingEndpoint(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $registration_repo = new FakeCreateBookingEndpointRegistrationRepository();
        $entity_manager->repositories[Registration::class] = $registration_repo;
        $registration_info_repo = new FakeCreateBookingEndpointRegistrationInfoRepository();
        $entity_manager->repositories[RegistrationInfo::class] = $registration_info_repo;
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        WithUtilsCache::get('authUtils')->has_permission_by_query = ['any' => true];
        $endpoint = new CreateBookingEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'meta' => [
                'onOff' => true,
                'ownerUserId' => null,
                'ownerRoleId' => null,
            ],
            'data' => [
                'registrationId' => 'Registration:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
                'values' => [
                    '0-vorname' => 'Simon',
                    '1-nachname' => 'Hatt',
                ],
            ],
        ]);

        $this->assertSame([
            'status' => 'OK',
            'id' => 'Booking:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $this->assertSame(1, count($entity_manager->persisted));
        $this->assertSame(1, count($entity_manager->flushed_persisted));
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $booking = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $booking->getId());
        $this->assertSame(264, $booking->getRegistration()->getId());
        $this->assertSame('{"0-vorname":"Simon","1-nachname":"Hatt"}', $booking->getFormData());
        $this->assertSame(Fake\FakeUsers::defaultUser(), $booking->getUser());

        $this->assertSame([
            [$booking, 1, null, null],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
