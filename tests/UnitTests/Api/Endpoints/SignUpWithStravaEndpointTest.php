<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\SignUpWithStravaEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\SignUpWithStravaEndpoint
 */
final class SignUpWithStravaEndpointTest extends UnitTestCase {
    public function testSignUpWithStravaEndpointIdent(): void {
        $endpoint = new SignUpWithStravaEndpoint();
        $this->assertSame('SignUpWithStravaEndpoint', $endpoint->getIdent());
    }

    public function testSignUpWithStravaEndpointWithoutInput(): void {
        $endpoint = new SignUpWithStravaEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'stravaUser' => null,
                'accessToken' => null,
                'refreshToken' => null,
                'expiresAt' => null,
                'firstName' => null,
                'lastName' => null,
                'username' => null,
                'email' => null,
                'phone' => null,
                'gender' => null,
                'birthdate' => null,
                'street' => null,
                'postalCode' => null,
                'city' => null,
                'region' => null,
                'countryCode' => null,
                'siCardNumber' => null,
                'solvNumber' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame([
                'stravaUser' => [['.' => ['Feld darf nicht leer sein.']]],
                'accessToken' => [['.' => ['Feld darf nicht leer sein.']]],
                'refreshToken' => [['.' => ['Feld darf nicht leer sein.']]],
                'expiresAt' => [['.' => ['Feld darf nicht leer sein.']]],
                'firstName' => [['.' => ['Feld darf nicht leer sein.']]],
                'lastName' => [['.' => ['Feld darf nicht leer sein.']]],
                'username' => [['.' => ['Feld darf nicht leer sein.']]],
                'email' => [['.' => ['Feld darf nicht leer sein.']]],
                'street' => [['.' => ['Feld darf nicht leer sein.']]],
                'postalCode' => [['.' => ['Feld darf nicht leer sein.']]],
                'city' => [['.' => ['Feld darf nicht leer sein.']]],
                'region' => [['.' => ['Feld darf nicht leer sein.']]],
                'countryCode' => [['.' => ['Feld darf nicht leer sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithStravaEndpointWithValidData(): void {
        $endpoint = new SignUpWithStravaEndpoint();
        $endpoint->runtimeSetup();
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call([
            'stravaUser' => 'fakeStravaUser',
            'accessToken' => 'fakeAccessToken',
            'refreshToken' => 'fakeRefreshToken',
            'expiresAt' => '1992-08-05 13:27:00',
            'firstName' => 'fakeFirstName',
            'lastName' => 'fakeLastName',
            'username' => 'fakeUsername',
            'email' => 'fakeEmail',
            'phone' => '+41441234567',
            'gender' => null,
            'birthdate' => null,
            'street' => 'fakeStreet',
            'postalCode' => 'fakePostalCode',
            'city' => 'fakeCity',
            'region' => 'fakeRegion',
            'countryCode' => 'fakeCountryCode',
            'siCardNumber' => 1234567,
            'solvNumber' => 'JACK7NORRIS',
        ]);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
            'user_id' => strval(Fake\FakeEntityManager::AUTO_INCREMENT_ID),
            'auth_user' => 'fakeUsername',
            'auth_user_id' => strval(Fake\FakeEntityManager::AUTO_INCREMENT_ID),
        ], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_STRAVA',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }
}
