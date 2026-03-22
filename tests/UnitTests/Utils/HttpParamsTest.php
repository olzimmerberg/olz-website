<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;
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
 * @covers \Olz\Utils\HttpParams
 */
final class HttpParamsTest extends UnitTestCase {
    public function testHttpParams(): void {
        $utils = new PhpStanUtils();
        $this->assertEquals([
            'Test' => ['namespace' => FakeForTypeImport::class, 'name' => 'Test'],
        ], $utils->getAliases(TestOnlyHttpParams::class));
    }
}
