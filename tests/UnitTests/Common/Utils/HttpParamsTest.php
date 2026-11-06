<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Common\Utils;

use Olz\Common\Utils\HttpParams;
use Olz\Tests\UnitTests\Common\UnitTestCase;
use PhpTypeScriptApi\PhpStan\PhpStanUtils;

/**
 * @phpstan-type Test bool
 */
class FakeForTypeImport {
}

/**
 * @phpstan-import-type Test from FakeForTypeImport
 *
 * @extends HttpParams<array{test: Test}>
 */
class TestOnlyHttpParams extends HttpParams {
}

/**
 * @internal
 *
 * @covers \Olz\Common\Utils\HttpParams
 */
final class HttpParamsTest extends UnitTestCase {
    public function testHttpParams(): void {
        $utils = new PhpStanUtils();
        $this->assertEquals([
            'Test' => ['namespace' => FakeForTypeImport::class, 'name' => 'Test'],
        ], $utils->getAliases(TestOnlyHttpParams::class));
    }
}
