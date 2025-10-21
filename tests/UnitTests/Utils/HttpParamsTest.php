<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
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
        $params_class = new \ReflectionClass(TestOnlyHttpParams::class);
        $utils = new PhpStanUtils();
        $php_doc_node = $utils->parseDocComment(
            $params_class->getDocComment(),
            $params_class->getFileName() ?: null,
        );
        $this->assertEquals([
            'Test' => new IdentifierTypeNode('bool'),
        ], $utils->getAliases($php_doc_node));
    }
}
