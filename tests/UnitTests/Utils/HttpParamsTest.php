<?php

declare(strict_types=1);

namespace Olz\Tests\UnitTests\Utils;

use Olz\Tests\UnitTests\Common\UnitTestCase;
use Olz\Utils\HttpParams;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;

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
        $params = new TestOnlyHttpParams();
        $params_class = new \ReflectionClass(TestOnlyHttpParams::class);
        $php_doc_node = $params->phpStanUtils->parseDocComment(
            $params_class->getDocComment(),
            $params_class->getFileName() ?: null,
        );
        $this->assertEquals([
            'Test' => new IdentifierTypeNode('bool'),
        ], $params->phpStanUtils->getAliases($php_doc_node));
    }
}
