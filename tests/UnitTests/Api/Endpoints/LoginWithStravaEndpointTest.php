<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\LoginWithStravaEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Fetchers\StravaFetcher;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\MemorySession;
use Olz\Utils\StravaUtils;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class FakeLoginWithStravaEndpointStravaFetcher extends StravaFetcher {
    protected $response;

    public function __construct($response) {
        $this->response = $response;
    }

    public function fetchTokenDataForCode($token_request_data) {
        return $this->response;
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\LoginWithStravaEndpoint
 */
final class LoginWithStravaEndpointTest extends UnitTestCase {
    public function testLoginWithStravaEndpointIdent(): void {
        $endpoint = new LoginWithStravaEndpoint();
        $this->assertSame('LoginWithStravaEndpoint', $endpoint->getIdent());
    }

    public function testLoginWithStravaEndpointWithoutInput(): void {
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([]);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($strava_fetcher);
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setStravaUtils($strava_utils);
        try {
            $endpoint->call(['code' => null]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'WARNING Bad user request',
            ], $this->getLogs());
            $this->assertSame([
                'code' => [['.' => ['Feld darf nicht leer sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testLoginWithStravaEndpointWithExistingUser(): void {
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'token_type' => 'fake_token_type',
            'expires_at' => 713014020,
            'refresh_token' => 'fake_refresh_token',
            'access_token' => 'fake_access_token',
            'athlete' => [
                'id' => 'fake_existing_id',
                'firstname' => 'fake_firstname',
                'lastname' => 'fake_lastname',
                'sex' => 'F',
                'city' => 'fake_city',
                'state' => 'fake_state',
                'country' => 'fake_country',
                'profile' => 'fake_profile',
            ],
        ]);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($strava_fetcher);
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(['code' => 'fake-code']);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'tokenType' => null,
            'expiresAt' => null,
            'refreshToken' => null,
            'accessToken' => null,
            'userIdentifier' => null,
            'firstName' => null,
            'lastName' => null,
            'gender' => null,
            'city' => null,
            'region' => null,
            'country' => null,
            'profilePictureUrl' => null,
            'status' => 'AUTHENTICATED',
        ], $result);
        $this->assertSame([
            'auth' => null,
            'root' => null,
            'user' => 'user',
            'user_id' => '1',
            'auth_user' => 'user',
            'auth_user_id' => '1',
        ], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_STRAVA',
                'timestamp' => null,
                'username' => 'user',
            ],
        ], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testLoginWithStravaEndpointWithNewUser(): void {
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'token_type' => 'fake_token_type',
            'expires_at' => 713021220,
            'refresh_token' => 'fake_refresh_token',
            'access_token' => 'fake_access_token',
            'athlete' => [
                'id' => 'fake_inexistent_id',
                'firstname' => 'fake_firstname',
                'lastname' => 'fake_lastname',
                'sex' => 'F',
                'city' => 'fake_city',
                'state' => 'fake_state',
                'country' => 'fake_country',
                'profile' => 'fake_profile',
            ],
        ]);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($strava_fetcher);
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(['code' => 'fake-code']);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'status' => 'NOT_REGISTERED',
            'tokenType' => 'fake_token_type',
            'expiresAt' => '1992-08-05 13:27:00',
            'refreshToken' => 'fake_refresh_token',
            'accessToken' => 'fake_access_token',
            'userIdentifier' => 'fake_inexistent_id',
            'firstName' => 'fake_firstname',
            'lastName' => 'fake_lastname',
            'gender' => 'F',
            'city' => 'fake_city',
            'region' => 'fake_state',
            'country' => 'fake_country',
            'profilePictureUrl' => 'fake_profile',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }

    public function testLoginWithStravaEndpointWithInvalidCode(): void {
        $strava_fetcher = new FakeLoginWithStravaEndpointStravaFetcher([
            'message' => 'Bad Request',
            'errors' => [
                [
                    'resource' => 'Application',
                    'field' => 'code',
                    'code' => 'invalid',
                ],
            ],
        ]);
        $strava_utils = new StravaUtils();
        $strava_utils->setClientId('fake-client-id');
        $strava_utils->setClientSecret('fake-client-secret');
        $strava_utils->setRedirectUrl('fake-redirect-url');
        $strava_utils->setStravaFetcher($strava_fetcher);
        $endpoint = new LoginWithStravaEndpoint();
        $endpoint->runtimeSetup();
        $endpoint->setStravaUtils($strava_utils);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);

        $result = $endpoint->call(['code' => 'invalid-code']);

        $this->assertSame([
            'INFO Valid user request',
            'INFO Valid user response',
        ], $this->getLogs());
        $this->assertSame([
            'tokenType' => null,
            'expiresAt' => null,
            'refreshToken' => null,
            'accessToken' => null,
            'userIdentifier' => null,
            'firstName' => null,
            'lastName' => null,
            'gender' => null,
            'city' => null,
            'region' => null,
            'country' => null,
            'profilePictureUrl' => null,
            'status' => 'INVALID_CODE',
        ], $result);
        $this->assertSame([], $session->session_storage);
        $entity_manager = WithUtilsCache::get('entityManager');
        $this->assertSame([], $entity_manager->getRepository(AuthRequest::class)->auth_requests);
    }
}
