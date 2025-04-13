<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Api\Endpoints;

use Olz\Api\Endpoints\ResetPasswordEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class DeterministicResetPasswordEndpoint extends ResetPasswordEndpoint {
    public function __construct() {
        parent::__construct();
        $this->setServer(['REMOTE_ADDR' => '1.2.3.4']);
    }

    protected function getRandomPassword(): string {
        return 'fake-new-password';
    }
}

/**
 * @internal
 *
 * @covers \Olz\Api\Endpoints\ResetPasswordEndpoint
 */
final class ResetPasswordEndpointTest extends UnitTestCase {
    public function testResetPasswordEndpointWithoutInput(): void {
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                'usernameOrEmail' => ["Fehlender Schlüssel: usernameOrEmail."],
                'captchaToken' => ["Fehlender Schlüssel: captchaToken."],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
            $this->assertSame([
                "WARNING Bad user request",
            ], $this->getLogs());
        }
    }

    public function testResetPasswordEndpointWithNullInput(): void {
        $endpoint = new DeterministicResetPasswordEndpoint();
        $endpoint->runtimeSetup();
        try {
            $endpoint->call([
                'usernameOrEmail' => null,
                'captchaToken' => null,
            ]);
            $this->fail('Exception expected.');
        } catch (HttpError $httperr) {
            $this->assertSame([
                "WARNING Bad user request",
            ], $this->getLogs());
            $this->assertSame([
                'usernameOrEmail' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                'captchaToken' => [['.' => ['Wert muss vom Typ non-empty-string sein.']]],
                // @phpstan-ignore-next-line
            ], $httperr->getPrevious()->getValidationErrors());
        }
    }

    public function testResetPasswordEndpoint(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = WithUtilsCache::get('emailUtils');
        $email_utils->setMailer($mailer);
        $endpoint->runtimeSetup();
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
            'captchaToken' => 'fake-captcha-token',
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
            'DEBUG Sending email to "Admin Istrator" <admin-user@staging.olzimmerberg.ch> ()',
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
        $mailer = $this->createMock(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = WithUtilsCache::get('emailUtils');
        $email_utils->setMailer($mailer);
        $endpoint->runtimeSetup();
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
            'captchaToken' => 'fake-captcha-token',
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
            'DEBUG Sending email to "Vorstand Mitglied" <vorstand-user@staging.olzimmerberg.ch> ()',
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
        $mailer = $this->createMock(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = WithUtilsCache::get('emailUtils');
        $email_utils->setMailer($mailer);
        $endpoint->runtimeSetup();
        $mailer->expects($this->exactly(0))->method('send');

        $result = $endpoint->call([
            'usernameOrEmail' => 'invalid',
            'captchaToken' => 'fake-captcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "NOTICE Password reset for unknown user: invalid.",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'DENIED'], $result);
    }

    public function testResetPasswordEndpointInvalidcaptchaToken(): void {
        $mailer = $this->createMock(MailerInterface::class);
        $endpoint = new DeterministicResetPasswordEndpoint();
        $email_utils = WithUtilsCache::get('emailUtils');
        $email_utils->setMailer($mailer);
        $endpoint->runtimeSetup();
        $mailer->expects($this->exactly(0))->method('send');

        $result = $endpoint->call([
            'usernameOrEmail' => 'admin',
            'captchaToken' => 'invalid-captcha-token',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame(['status' => 'DENIED'], $result);
    }
}
