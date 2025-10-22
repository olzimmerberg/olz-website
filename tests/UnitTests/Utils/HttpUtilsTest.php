<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Entity\Counter;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;
use Olz\Utils\HttpUtils;
use Olz\Utils\WithUtilsCache;
use PhpTypeScriptApi\PhpStan\IsoDate;
use Symfony\Component\HttpFoundation\Request;

/**
 * TODO: string -> int and string -> float.
 *
 * @phpstan-import-type TestAlias from ExitException
 *
 * @extends HttpParams<array{
 *   argInt: numeric-string,
 *   argMaybeFloat?: ?numeric-string,
 *   stringOrNull: ?non-empty-string,
 *   date: IsoDate,
 *   alias: TestAlias,
 * }>
 */
class TestParams extends HttpParams {
}

/**
 * @phpstan-type TestAlias array<string>
 */
class ExitException extends \Exception {
}

class TestOnlyHttpUtils extends HttpUtils {
    public ?int $http_response_code = null;
    /** @var array<string> */
    public array $http_header_lines = [];
    public ?string $http_body = null;

    protected function sendHttpResponseCode(int $http_response_code): void {
        $this->http_response_code = $http_response_code;
    }

    protected function sendHeader(string $http_header_line): void {
        $this->http_header_lines[] = $http_header_line;
    }

    protected function sendHttpBody(string $http_body): void {
        $this->http_body = $http_body;
    }

    protected function exitExecution(): void {
        throw new ExitException();
    }
}

/**
 * @internal
 *
 * @covers \Olz\Utils\HttpUtils
 */
final class HttpUtilsTest extends UnitTestCase {
    public function testGetBotRegexes(): void {
        $utils = new TestOnlyHttpUtils();
        $this->assertContains('/googlebot/i', $utils->getBotRegexes());
    }

    public function testIsBot(): void {
        $utils = new TestOnlyHttpUtils();
        $this->assertTrue($utils->isBot('(KHTML, like Gecko; compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'));
        $this->assertTrue($utils->isBot('(compatible; Googlebot/2.1; +http://www.google.com/bot.html)'));
        $this->assertTrue($utils->isBot('(compatible; Google-Read-Aloud; +https://support.google.com/webmasters/answer/1061943)'));
        $this->assertTrue($utils->isBot('(compatible; GoogleOther)'));
        $this->assertTrue($utils->isBot('Safari/601.2.4 facebookexternalhit/1.1 Facebot Twitterbot/1.0'));
        $this->assertTrue($utils->isBot('(Applebot/0.1; +http://www.apple.com/go/applebot)'));
        $this->assertTrue($utils->isBot('(compatible; YandexBot/3.0; +http://yandex.com/bots)'));
        $this->assertTrue($utils->isBot('(Ecosia android@140.0.0.0)'));
        $this->assertTrue($utils->isBot('(Ecosia ios@11.4.0.2531)'));
        $this->assertTrue($utils->isBot('(compatible; phpservermon/3.5.2; +https://github.com/phpservermon/phpservermon)'));
        $this->assertTrue($utils->isBot('OlzSystemTest/1.0'));
        $this->assertTrue($utils->isBot('(compatible; wtfbot/1.0)'));
        $this->assertTrue($utils->isBot('(compatible; VelenPublicWebCrawler/1.0; +https://velen.io)'));
        $this->assertTrue($utils->isBot('(compatible; SeznamBot/4.0; +https://o-seznam.cz/napoveda/vyhledavani/en/seznambot-crawler/)'));

        $this->assertFalse($utils->isBot(''));
        // Chrome
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/141.0.7390.96 Mobile/15E148 Safari/604.1'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.7390.112 Mobile Safari/537.36'));
        // Firefox
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:143.0) Gecko/20100101 Firefox/143.0'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:143.0) Gecko/20100101 Firefox/143.0'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (X11; Linux x86_64; rv:143.0) Gecko/20100101 Firefox/143.0'));
        // Edge
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36 Edg/140.0.0.0'));
        // Safari
        $this->assertFalse($utils->isBot('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15'));
        $this->assertFalse($utils->isBot('Mozilla/5.0 (iPhone; CPU iPhone OS 18_7_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0 Mobile/15E148 Safari/604.1'));
    }

    public function testCountRequest(): void {
        $entity_manager = WithUtilsCache::get('entityManager');
        $utils = new TestOnlyHttpUtils();

        $utils->countRequest(new Request(['k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3']), ['k1', 'k3']);

        $this->assertSame(['/?k1=v1&k3=v3'], $entity_manager->repositories[Counter::class]->records);
    }

    public function testDieWithHttpError(): void {
        $utils = new TestOnlyHttpUtils();

        try {
            $utils->dieWithHttpError(404);
            $this->fail('Error expected');
        } catch (ExitException $e) {
            $this->assertSame(404, $utils->http_response_code);
            $this->assertStringContainsString('Schilf', $utils->http_body ?? '');
        }
    }

    public function testRedirect(): void {
        $utils = new TestOnlyHttpUtils();

        try {
            $utils->redirect('https://staging.olzimmerberg.ch', 308);
            $this->fail('Error expected');
        } catch (ExitException $e) {
            $this->assertSame(308, $utils->http_response_code);
            $this->assertSame(['Location: https://staging.olzimmerberg.ch'], $utils->http_header_lines);
            $this->assertStringContainsString('Weiterleitung', $utils->http_body ?? '');
        }
    }

    public function testValidateGetParamsMinimal(): void {
        $utils = new TestOnlyHttpUtils();

        $this->assertEquals([
            'argInt' => '3',
            'stringOrNull' => null,
            'date' => new IsoDate('2024-12-25'),
            'alias' => [],
        ], $utils->validateGetParams(TestParams::class, [
            'argInt' => '3',
            'stringOrNull' => null,
            'date' => '2024-12-25',
            'alias' => [],
        ]));
    }

    public function testValidateGetParamsMaximal(): void {
        $utils = new TestOnlyHttpUtils();

        $this->assertEquals([
            'argInt' => '3',
            'argMaybeFloat' => '3.14',
            'stringOrNull' => 'test',
            'date' => new IsoDate('2024-12-25'),
            'alias' => ['one', 'two'],
        ], $utils->validateGetParams(TestParams::class, [
            'argInt' => '3',
            'argMaybeFloat' => '3.14',
            'stringOrNull' => 'test',
            'date' => '2024-12-25',
            'alias' => ['one', 'two'],
        ]));
    }

    public function testValidateGetParamsNonNullableError(): void {
        $utils = new TestOnlyHttpUtils();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => null,
                'argMaybeFloat' => null,
                'stringOrNull' => null,
                'date' => '2024-12-25',
                'alias' => [],
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body ?? '');
        }
    }

    public function testValidateGetParamsMissingParamError(): void {
        $utils = new TestOnlyHttpUtils();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => '3',
                'argMaybeFloat' => null,
                'date' => '2024-12-25',
                'alias' => [],
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body ?? '');
        }
    }

    public function testValidateGetParamsRedundantParamError(): void {
        $utils = new TestOnlyHttpUtils();
        try {
            $utils->validateGetParams(TestParams::class, [
                'argInt' => null,
                'argMaybeFloat' => null,
                'stringOrNull' => null,
                'date' => '2024-12-25',
                'alias' => [],
                'redundant' => 'yes',
            ]);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body ?? '');
        }
    }

    public function testValidateGetParamsEmptyError(): void {
        $utils = new TestOnlyHttpUtils();
        try {
            $utils->validateGetParams(TestParams::class, []);
            $this->fail('Error expected');
        } catch (ExitException $exc) {
            $this->assertSame(400, $utils->http_response_code);
            $this->assertSame([], $utils->http_header_lines);
            $this->assertStringContainsString('400', $utils->http_body ?? '');
        }
    }
}
