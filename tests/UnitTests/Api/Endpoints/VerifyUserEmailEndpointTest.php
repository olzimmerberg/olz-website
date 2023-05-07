<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\VerifyUserEmailEndpoint;
use Olz\Exceptions\RecaptchaDeniedException;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\VerifyUserEmailEndpoint
 */
final class VerifyUserEmailEndpointTest extends UnitTestCase {
    public function testVerifyUserEmailEndpointIdent(): void {
        $endpoint = new VerifyUserEmailEndpoint();
        $this->assertSame('VerifyUserEmailEndpoint', $endpoint->getIdent());
    }

    public function testVerifyUserEmailEndpoint(): void {
        $user = Fake\FakeUsers::defaultUser();
        WithUtilsCache::get('authUtils')->current_user = $user;
        $email_utils = new Fake\FakeEmailUtils();
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([['user' => $user]], $email_utils->email_verification_emails_sent);
    }

    public function testVerifyUserEmailEndpointWithoutInput(): void {
        $email_utils = new Fake\FakeEmailUtils();
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testVerifyUserEmailEndpointWithNullInput(): void {
        $email_utils = new Fake\FakeEmailUtils();
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'recaptchaToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Fehlerhafte Eingabe', $httperr->getMessage());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testVerifyUserEmailEndpointUnauthenticated(): void {
        $email_utils = new Fake\FakeEmailUtils();
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        try {
            $endpoint->call([
                'recaptchaToken' => 'fake-recaptcha-token',
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame('Nicht eingeloggt!', $httperr->getMessage());
            $this->assertSame([
                "INFO Valid user request",
                "WARNING HTTP error 401",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testVerifyUserEmailEndpointInvalidRecaptchaToken(): void {
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        $email_utils = new Fake\FakeEmailUtils();
        $email_utils->send_email_verification_email_error = new RecaptchaDeniedException('test');
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'recaptchaToken' => 'invalid-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Error sending fake verification email",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'DENIED'], $result);
        $this->assertSame([], $email_utils->email_verification_emails_sent);
    }

    public function testVerifyUserEmailEndpointErrorSending(): void {
        WithUtilsCache::get('authUtils')->current_user = Fake\FakeUsers::defaultUser();
        $email_utils = new Fake\FakeEmailUtils();
        $email_utils->send_email_verification_email_error = new \Exception('test');
        $entity_manager = new Fake\FakeEntityManager();
        $logger = Fake\FakeLogger::create();
        $endpoint = new VerifyUserEmailEndpoint();
        $endpoint->setEmailUtils($email_utils);
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);

        $result = $endpoint->call([
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "ERROR Error sending fake verification email",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([], $email_utils->email_verification_emails_sent);
    }
}
