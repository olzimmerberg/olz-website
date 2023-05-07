<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ResetPasswordEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

class DeterministicResetPasswordEndpoint extends ResetPasswordEndpoint {
    public function __construct() {
        $this->setServer(['REMOTE_ADDR' => '1.2.3.4']);
    }

    protected function getRandomPassword() {
        return 'fake-new-password';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\ResetPasswordEndpoint
 */
final class ResetPasswordEndpointTest extends UnitTestCase {
    public function testResetPasswordEndpointIdent(): void {
        $endpoint = new DeterministicResetPasswordEndpoint();
        $this->assertSame('ResetPasswordEndpoint', $endpoint->getIdent());
    }

    public function testResetPasswordEndpointWithoutInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ["Fehlender Schlüssel: usernameOrEmail."],
                'recaptchaToken' => ["Fehlender Schlüssel: recaptchaToken."],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testResetPasswordEndpointWithNullInput(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setLog($logger);
        try {
            $result = $endpoint->call([
                'usernameOrEmail' => null,
                'recaptchaToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Feld darf nicht leer sein.']]],
                'recaptchaToken' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $logger->handler->getPrettyRecords());
        }
    }

    public function testResetPasswordEndpoint(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**
        
        Hallo Admin,
        
        *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier](http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0}) oder auf folgenden Link:
        
        http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0
        
        Dein neues Passwort lautet dann nachher:
        `fake-new-password`

        ZZZZZZZZZZ;
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [
                'user' => Fake\FakeUsers::adminUser(),
                'from' => ['fake@staging.olzimmerberg.ch', 'OL Zimmerberg'],
                'replyTo' => null,
                'subject' => '[OLZ] Passwort zurücksetzen',
                'body' => $expected_text,
                'altBody' => $expected_text,
            ],
        ], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Password reset email sent to user (2).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointUsingEmailErrorSending(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $vorstand_user = Fake\FakeUsers::vorstandUser();
        $vorstand_user->setFirstName('provoke_error');

        $result = $endpoint->call([
            'usernameOrEmail' => 'vorstand@staging.olzimmerberg.ch',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([], WithUtilsCache::get('emailUtils')->olzMailer->emails_sent);
        $this->assertSame([
            "INFO Valid user request",
            "CRITICAL Error sending password reset email to user (3): Provoked Mailer Error",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointInvalidUser(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'usernameOrEmail' => 'invalid',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'DENIED'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Password reset for unknown user: invalid.",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointInvalidRecaptchaToken(): void {
        $logger = Fake\FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new Fake\FakeEntityManager();
        $user_repo = new Fake\FakeUserRepository();
        $entity_manager->repositories[User::class] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $endpoint->setLog($logger);
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'invalid-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'DENIED'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
