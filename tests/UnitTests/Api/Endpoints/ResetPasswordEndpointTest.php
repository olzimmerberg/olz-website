<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ResetPasswordEndpoint;
use Olz\Tests\Fake;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

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
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $this->assertSame('ResetPasswordEndpoint', $endpoint->getIdent());
    }

    public function testResetPasswordEndpointWithoutInput(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
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
            ], $this->getLogs());
        }
    }

    public function testResetPasswordEndpointWithNullInput(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        try {
            $result = $endpoint->call([
                'usernameOrEmail' => null,
                'recaptchaToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Feld darf nicht leer sein.']]],
                'recaptchaToken' => [['.' => ['Feld darf nicht leer sein.']]],
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testResetPasswordEndpoint(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $artifacts = [];
        $mailer->expects($this->exactly(1))->method('send')->with(
            $this->callback(function (Email $email) use (&$artifacts) {
                $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                return true;
            }),
            null,
        );

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**
        
        Hallo Admin,
        
        *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier](http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0}) oder auf folgenden Link:
        
        http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjIsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0
        
        Dein neues Passwort lautet dann nachher:
        `fake-new-password`

        ZZZZZZZZZZ;
        $this->assertSame([
            "INFO Valid user request",
            "INFO Password reset email sent to user (2).",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'OK'], $result);
        $this->assertSame([
            <<<ZZZZZZZZZZ
            From: 
            Reply-To: 
            To: "Admin Istrator" <admin-user@staging.olzimmerberg.ch>
            Cc: 
            Bcc: 
            Subject: [OLZ] Passwort zurücksetzen

            {$expected_text}


            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            {$expected_text}


            olz_logo
            ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testResetPasswordEndpointUsingEmailErrorSending(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());
        $artifacts = [];
        $mailer
            ->expects($this->exactly(1))
            ->method('send')
            ->with(
                $this->callback(function (Email $email) use (&$artifacts) {
                    $artifacts['email'] = [...($artifacts['email'] ?? []), $email];
                    return true;
                }),
                null,
            )
            ->will($this->throwException(new \Exception('mocked-error')))
        ;

        $result = $endpoint->call([
            'usernameOrEmail' => 'vorstand@staging.olzimmerberg.ch',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $expected_text = <<<'ZZZZZZZZZZ'
        **!!! Falls du nicht soeben dein Passwort zurücksetzen wolltest, lösche diese E-Mail !!!**
        
        Hallo Vorstand,
        
        *Falls du dein Passwort zurückzusetzen möchtest*, klicke [hier](http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjMsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0}) oder auf folgenden Link:
        
        http://fake-base-url/_/email_reaktion?token=eyJhY3Rpb24iOiJyZXNldF9wYXNzd29yZCIsInVzZXIiOjMsIm5ld19wYXNzd29yZCI6ImZha2UtbmV3LXBhc3N3b3JkIn0
        
        Dein neues Passwort lautet dann nachher:
        `fake-new-password`

        ZZZZZZZZZZ;
        $this->assertSame([
            "INFO Valid user request",
            "CRITICAL Error sending password reset email to user (3): mocked-error",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'ERROR'], $result);
        $this->assertSame([
            <<<ZZZZZZZZZZ
            From: 
            Reply-To: 
            To: "Vorstand Mitglied" <vorstand-user@staging.olzimmerberg.ch>
            Cc: 
            Bcc: 
            Subject: [OLZ] Passwort zurücksetzen

            {$expected_text}


            <div style="text-align: right; float: right;">
                <img src="cid:olz_logo" alt="" style="width:150px;" />
            </div>
            <br /><br /><br />
            {$expected_text}


            olz_logo
            ZZZZZZZZZZ,
        ], array_map(function ($email) {
            return $this->emailUtils()->getComparableEmail($email);
        }, $artifacts['email']));
    }

    public function testResetPasswordEndpointInvalidUser(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'usernameOrEmail' => 'invalid',
            'recaptchaToken' => 'fake-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Password reset for unknown user: invalid.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'DENIED'], $result);
    }

    public function testResetPasswordEndpointInvalidRecaptchaToken(): void {
        $mailer = $this->createStub(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->setMailer($mailer);
        $endpoint->runtimeSetup();
        $endpoint->setRecaptchaUtils(new Fake\FakeRecaptchaUtils());

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'recaptchaToken' => 'invalid-recaptcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'DENIED'], $result);
    }
}
