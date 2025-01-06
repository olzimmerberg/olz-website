<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\Endpoints\CreateBookingEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\Fake\Entity\Users\FakeUser;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;

/**
 * @internal
 *
 * @covers \Olz\Apps\Anmelden\Endpoints\CreateBookingEndpoint
 */
final class CreateBookingEndpointTest extends UnitTestCase {
    public function testCreateBookingEndpoint(): void {
        WithUtilsCache::get('authUtils')->current_user = FakeUser::defaultUser();
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
            'id' => 'Booking:'.Fake\FakeEntityManager::AUTO_INCREMENT_ID,
        ], $result);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertCount(1, $entity_manager->persisted);
        $this->assertCount(1, $entity_manager->flushed_persisted);
        $this->assertSame($entity_manager->persisted, $entity_manager->flushed_persisted);
        $booking = $entity_manager->persisted[0];
        $this->assertSame(Fake\FakeEntityManager::AUTO_INCREMENT_ID, $booking->getId());
        $this->assertSame(264, $booking->getRegistration()->getId());
        $this->assertSame('{"0-vorname":"Simon","1-nachname":"Hatt"}', $booking->getFormData());
        $this->assertSame(FakeUser::defaultUser(), $booking->getUser());

        $this->assertSame([
            [$booking, 1, 1, null],
        ], WithUtilsCache::get('entityUtils')->create_olz_entity_calls);
    }
}
