<?php

declare(strict_types=1);

use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../../../src/api/endpoints/ResetPasswordEndpoint.php';
require_once __DIR__.'/../../../../src/config/vendor/autoload.php';
require_once __DIR__.'/../../../../src/utils/session/MemorySession.php';
require_once __DIR__.'/../../../../src/utils/GeneralUtils.php';
require_once __DIR__.'/../../../fake/FakeUsers.php';
require_once __DIR__.'/../../../fake/FakeAuthUtils.php';
require_once __DIR__.'/../../../fake/FakeEmailUtils.php';
require_once __DIR__.'/../../../fake/FakeEntityManager.php';
require_once __DIR__.'/../../../fake/FakeEnvUtils.php';
require_once __DIR__.'/../../../fake/FakeLogger.php';
require_once __DIR__.'/../../../fake/FakeUserRepository.php';
require_once __DIR__.'/../../common/UnitTestCase.php';

class FakeResetPasswordEndpointGoogleFetcher {
    public function fetchRecaptchaVerification($siteverify_request_data) {
        $successful_request = [
            'secret' => 'some-secret-key',
            'response' => 'fake-recaptcha-token',
        ];
        if ($siteverify_request_data == $successful_request) {
            return ['success' => true];
        }
        $unsuccessful_request = [
            'secret' => 'some-secret-key',
            'response' => 'invalid-recaptcha-token',
        ];
        if ($siteverify_request_data == $unsuccessful_request) {
            return ['success' => false];
        }
        $null_request = [
            'secret' => 'some-secret-key',
            'response' => 'null-recaptcha-token',
        ];
        if ($siteverify_request_data == $null_request) {
            return null;
        }
        $request_json = json_encode($siteverify_request_data);
        throw new Exception("Unexpected Request: {$request_json}");
    }
}

class DeterministicResetPasswordEndpoint extends ResetPasswordEndpoint {
    protected function getRandomPassword() {
        return 'fake-new-password';
    }
}

/**
 * @internal
 * @covers \ResetPasswordEndpoint
 */
final class ResetPasswordEndpointTest extends UnitTestCase {
    public function testResetPasswordEndpointIdent(): void {
        $endpoint = new DeterministicResetPasswordEndpoint();
        $this->assertSame('ResetPasswordEndpoint', $endpoint->getIdent());
    }

    public function testResetPasswordEndpointWithoutInput(): void {
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setLogger($logger);
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
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setLogger($logger);
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
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $env_utils = new FakeEnvUtils();
        $endpoint->setEnvUtils($env_utils);
        $general_utils = new GeneralUtils();
        $endpoint->setGeneralUtils($general_utils);
        $google_fetcher = new FakeResetPasswordEndpointGoogleFetcher();
        $endpoint->setGoogleFetcher($google_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**
        
        Hallo ,
        
        *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier](http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0}) oder auf folgenden Link:
        
        http://fake-base-url/_/email_reaktion.php?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0
        
        Dein neues Passwort lautet dann:
        `fake-new-password`

        ZZZZZZZZZZ;
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            [$user_repo->admin_user, '[OLZ] Passwort zurücksetzen', $expected_text],
        ], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Password reset email sent to user (2).",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointUsingEmailErrorSending(): void {
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = new FakeEmailUtils();
        $endpoint->setEmailUtils($email_utils);
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $env_utils = new FakeEnvUtils();
        $endpoint->setEnvUtils($env_utils);
        $general_utils = new GeneralUtils();
        $endpoint->setGeneralUtils($general_utils);
        $google_fetcher = new FakeResetPasswordEndpointGoogleFetcher();
        $endpoint->setGoogleFetcher($google_fetcher);
        $endpoint->setLogger($logger);
        $vorstand_user = FakeUsers::vorstandUser();
        $vorstand_user->setFirstName('provoke_error');

        $result = $endpoint->call([
            'usernameOrEmail' => 'vorstand@test.olzimmerberg.ch',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([], $email_utils->olzMailer->emails_sent);
        $this->assertSame([
            "INFO Valid user request",
            "CRITICAL Error sending password reset email to user (3): Provoked Error",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointInvalidUser(): void {
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $env_utils = new FakeEnvUtils();
        $endpoint->setEnvUtils($env_utils);
        $google_fetcher = new FakeResetPasswordEndpointGoogleFetcher();
        $endpoint->setGoogleFetcher($google_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'invalid',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'DENIED'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }

    public function testResetPasswordEndpointInvalidRecaptchaToken(): void {
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $env_utils = new FakeEnvUtils();
        $endpoint->setEnvUtils($env_utils);
        $google_fetcher = new FakeResetPasswordEndpointGoogleFetcher();
        $endpoint->setGoogleFetcher($google_fetcher);
        $endpoint->setLogger($logger);

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

    public function testResetPasswordEndpointNullRecaptchaToken(): void {
        $logger = FakeLogger::create();
        $endpoint = new DeterministicResetPasswordEndpoint();
        $entity_manager = new FakeEntityManager();
        $user_repo = new FakeUserRepository();
        $entity_manager->repositories['User'] = $user_repo;
        $endpoint->setEntityManager($entity_manager);
        $env_utils = new FakeEnvUtils();
        $endpoint->setEnvUtils($env_utils);
        $google_fetcher = new FakeResetPasswordEndpointGoogleFetcher();
        $endpoint->setGoogleFetcher($google_fetcher);
        $endpoint->setLogger($logger);

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'null-recaptcha-token',
        ]);

        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $logger->handler->getPrettyRecords());
    }
}
