<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Captcha\Endpoints;

use Olz\Captcha\Endpoints\DecryptEmailTokenEndpoint;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\HttpError;

/**
 * @internal
 *
 * @covers \Olz\Captcha\Endpoints\DecryptEmailTokenEndpoint
 */
final class DecryptEmailTokenEndpointTest extends UnitTestCase {
    public function testDecryptEmailTokenEndpointNoAccess(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new DecryptEmailTokenEndpoint();
        $endpoint->runtimeSetup();

        try {
            $endpoint->call([
                'emailToken' => $this->generalUtils()->encrypt(
                    $this->envUtils()->getEncryptionKey('email-captcha'),
                    [
                        'email' => 'user+olz@gmail.com',
                        'text' => 'Benutzer',
                    ],
                ),
            ]);
            $this->fail('Error expected');
        } catch (HttpError $err) {
            $this->assertSame([
                "INFO Valid user request",
                "NOTICE HTTP error 403",
            ], $this->getLogs());
            $this->assertSame(403, $err->getCode());
        }
    }

    public function testDecryptEmailTokenEndpointAuthenticated(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => true,
        ];
        $endpoint = new DecryptEmailTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'emailToken' => $this->generalUtils()->encrypt(
                $this->envUtils()->getEncryptionKey('email-captcha'),
                [
                    'email' => 'user+olz@gmail.com',
                    'text' => 'Kontakt',
                ],
            ),
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'email' => $this->emailUtils()->obfuscateEmail('user+olz@gmail.com'),
            'text' => 'Kontakt',
            'subject' => null,
        ], $result);
    }

    public function testDecryptEmailTokenEndpointAccessWithToken(): void {
        WithUtilsCache::get('authUtils')->has_permission_by_query = [
            'any' => false,
        ];
        $endpoint = new DecryptEmailTokenEndpoint();
        $endpoint->runtimeSetup();

        $result = $endpoint->call([
            'emailToken' => $this->generalUtils()->encrypt(
                $this->envUtils()->getEncryptionKey('email-captcha'),
                [
                    'email' => 'user+olz@gmail.com',
                    'text' => 'Kontakt',
                    'subject' => 'Nächstes Kartentraining',
                ],
            ),
            'captchaToken' => 'valid',
        ]);

        $this->assertSame([
            "INFO Valid user request",
            "INFO Valid user response",
        ], $this->getLogs());
        $this->assertSame([
            'email' => $this->emailUtils()->obfuscateEmail('user+olz@gmail.com'),
            'text' => 'Kontakt',
            'subject' => 'Nächstes Kartentraining',
        ], $result);
    }
}
