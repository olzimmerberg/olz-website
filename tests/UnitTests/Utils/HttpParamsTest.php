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
    public function configure(): void {
        $this->phpStanUtils->registerTypeImport(FakeForTypeImport::class);
    }
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
        $php_doc_node = $params->phpStanUtils->parseDocComment($params_class->getDocComment());
        try {
            $params->phpStanUtils->getAliases($php_doc_node);
            $this->fail('Error expected');
        } catch (\Throwable $th) {
            $this->assertSame('Failed importing Test from FakeForTypeImport', $th->getMessage());
        }
        $params->configure();
        $this->assertEquals([
            'Test' => new IdentifierTypeNode('bool'),
        ], $params->phpStanUtils->getAliases($php_doc_node));
    }
}
