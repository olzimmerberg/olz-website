<?php

declare(strict_types=1);

use Monolog\Logger;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../fake/fake_user.php';
require_once __DIR__.'/../../../fake/fake_strava_link.php';
require_once __DIR__.'/../../../../src/api/endpoints/SignUpWithPasswordEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/model/index.php';
require_once __DIR__.'/../../../../src/utils/auth/StravaUtils.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';

class FakeSignUpWithPasswordEndpointEntityManager {
    public $persisted = [];
    public $flushed = [];
    private $repositories = [];

    public function __construct() {
        $this->repositories = [
            'AuthRequest' => new FakeSignUpWithPasswordEndpointAuthRequestRepository(),
        ];
    }

    public function getRepository($class) {
        return $this->repositories[$class] ?? null;
    }

    public function persist($object) {
        $this->persisted[] = $object;
    }

    public function flush() {
        $this->flushed = $this->persisted;
    }
}

class FakeSignUpWithPasswordEndpointAuthRequestRepository {
    public $auth_requests = [];
    public $can_authenticate = true;

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        $this->auth_requests[] = [
            'ip_address' => $ip_address,
            'action' => $action,
            'timestamp' => $timestamp,
            'username' => $username,
        ];
    }

    public function canAuthenticate($ip_address, $timestamp = null) {
        return $this->can_authenticate;
    }
}

/**
 * @internal
 * @covers \SignUpWithPasswordEndpoint
 */
final class SignUpWithPasswordEndpointTest extends TestCase {
    public function testSignUpWithPasswordEndpointIdent(): void {
        $endpoint = new SignUpWithPasswordEndpoint();
        $this->assertSame('SignUpWithPasswordEndpoint', $endpoint->getIdent());
    }

    public function testSignUpWithPasswordEndpointWithoutInput(): void {
        $entity_manager = new FakeSignUpWithPasswordEndpointEntityManager();
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLogger($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'firstName' => ['Feld darf nicht leer sein.'],
                'lastName' => ['Feld darf nicht leer sein.'],
                'username' => ['Feld darf nicht leer sein.'],
                'password' => ['Feld darf nicht leer sein.'],
                'email' => ['Feld darf nicht leer sein.'],
                'street' => ['Feld darf nicht leer sein.'],
                'postalCode' => ['Feld darf nicht leer sein.'],
                'city' => ['Feld darf nicht leer sein.'],
                'region' => ['Feld darf nicht leer sein.'],
                'countryCode' => ['Feld darf nicht leer sein.'],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testSignUpWithPasswordEndpointWithValidData(): void {
        $entity_manager = new FakeSignUpWithPasswordEndpointEntityManager();
        $logger = new Logger('SignUpWithPasswordEndpointTest');
        $endpoint = new SignUpWithPasswordEndpoint();
        $endpoint->setEntityManager($entity_manager);
        $session = new MemorySession();
        $endpoint->setSession($session);
        $endpoint->setServer(['REMOTE_ADDR' => '1.2.3.4']);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'firstName' => 'fakeFirstName',
            'lastName' => 'fakeLastName',
            'username' => 'fakeUsername',
            'password' => 'securePassword',
            'email' => 'fakeEmail',
            'street' => 'fakeStreet',
            'postalCode' => 'fakePostalCode',
            'city' => 'fakeCity',
            'region' => 'fakeRegion',
            'countryCode' => 'fakeCountryCode',
        ]);

        $this->assertSame([
            'status' => 'OK',
        ], $result);
        $this->assertSame([
            'auth' => '',
            'root' => null,
            'user' => 'fakeUsername',
        ], $session->session_storage);
        $this->assertSame([
            [
                'ip_address' => '1.2.3.4',
                'action' => 'AUTHENTICATED_PASSWORD',
                'timestamp' => null,
                'username' => 'fakeUsername',
            ],
        ], $entity_manager->getRepository('AuthRequest')->auth_requests);
    }
}
